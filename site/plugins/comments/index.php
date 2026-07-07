<?php

use Kirby\Cms\App;
use Kirby\Http\Response;
use Kirby\Toolkit\Str;
use RealTroll\Comments\CommentNotification;
use RealTroll\Comments\CommentPage;
use RealTroll\Comments\SubmissionGuards;

load([
    'RealTroll\\Comments\\CommentHtml' => 'classes/CommentHtml.php',
    'RealTroll\\Comments\\CommentNotification' => 'classes/CommentNotification.php',
    'RealTroll\\Comments\\CommentPage' => 'classes/CommentPage.php',
    'RealTroll\\Comments\\CommentParsedown' => 'classes/CommentParsedown.php',
    'RealTroll\\Comments\\CommentRenderer' => 'classes/CommentRenderer.php',
    'RealTroll\\Comments\\SubmissionGuards' => 'classes/SubmissionGuards.php',
    'RealTroll\\Comments\\Turnstile' => 'classes/Turnstile.php',
    'RealTroll\\Comments\\Verdict' => 'classes/Verdict.php',
], __DIR__);

App::plugin('realtroll/comments', [
    'blueprints' => [
        'pages/comment' => __DIR__ . '/blueprints/pages/comment.yml',
    ],
    'pageModels' => [
        'comment' => CommentPage::class,
    ],
    'templates' => [
        'comment' => __DIR__ . '/templates/comment.php',
    ],
    'routes' => [
        [
            'pattern' => 'kommentare',
            'method' => 'POST',
            'action' => function () {
                $kirby   = App::instance();
                $request = $kirby->request();
                $verdict = (new SubmissionGuards())->evaluate($request);

                if (!$verdict->accepted) {
                    // The text stays client-side; the frontend re-shows it. `code`
                    // is the stable branch key (csrf → retry, turnstile → reset).
                    return Response::json([
                        'field'   => $verdict->field,
                        'code'    => $verdict->code,
                        'message' => $verdict->message,
                    ], 422);
                }

                $article = $verdict->article;

                // Fixed allowlist – never spread `$request->data()`. `author` is
                // added only from the Verdict (a server-side identity), so a POSTed
                // `author` field cannot leak into the stored comment.
                $content = [
                    'title'    => 'Kommentar von ' . $verdict->name,
                    'name'     => $verdict->name,
                    'text'     => $verdict->text,
                    'date'     => date('c'),
                    'parentId' => $verdict->parentId ?? '',
                ];

                if ($verdict->author !== null) {
                    $content['author'] = $verdict->author;
                }

                try {
                    $comment = $kirby->impersonate(
                        'kirby',
                        fn () => $article->createChild([
                            'slug'     => 'comment-' . Str::random(16, 'alphaNum'),
                            'template' => 'comment',
                            'content'  => $content,
                        ])->changeStatus('unlisted')
                    );
                } catch (\Throwable) {
                    // A failed write must surface as an announceable form error,
                    // not a bare 500 that reads like a network failure.
                    return Response::json([
                        'field'   => 'form',
                        'code'    => 'store',
                        'message' => 'Dein Kommentar konnte nicht gespeichert werden. Bitte versuche es später erneut.',
                    ], 500);
                }

                try {
                    CommentNotification::send($comment);
                } catch (\Throwable) {
                }

                return Response::json([
                    'html' => snippet('components/comments/thread', [
                        'comments'        => $article->comments()->sortBy('date', 'asc'),
                        'dateFormatter'   => new IntlDateFormatter('de_DE', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT),
                        'acceptsComments' => $article->acceptsComments(),
                    ], true),
                    'anchor' => 'kommentar-' . $comment->slug(),
                ]);
            },
        ],
        [
            'pattern' => 'kommentare/token',
            'method' => 'GET',
            'action' => function () {
                $kirby = App::instance();

                return Response::json(
                    [
                        'csrf'   => $kirby->csrf(),
                        'author' => $kirby->user()?->name()->value(),
                    ],
                    200,
                    null,
                    ['Cache-Control' => 'no-store']
                );
            },
        ],
    ],
]);
