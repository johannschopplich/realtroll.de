<?php

$turnstileSitekey = kirby()->option('realtroll.comments.turnstile.sitekey');

?>
<div
  class="
    [--frame:var(--tw-color-stone-400)]
    max-md:-mx-lg max-md:w-[calc(100%+2*var(--spacing-lg))]
  "
>
  <div
    class="
      flex items-stretch
      after:content-[''] after:flex-1 after:self-stretch after:border-b-2 after:border-[color:var(--frame)]
      max-md:before:content-[''] max-md:before:w-[var(--spacing-lg)] max-md:before:self-stretch max-md:before:border-b-2 max-md:before:border-[color:var(--frame)]
    "
  >
    <h3
      class="
        px-3 py-2
        font-heading text-lg leading-none text-primary-700
        bg-white border-2 border-b-0 border-[color:var(--frame)]
        shadow-[inset_3px_3px_0_var(--tw-color-stone-200)]
      "
    >Vorsicht, Troll liest mit!</h3>
  </div>
  <form
    data-comment-form
    class="
      relative flex flex-col
      gap-lg px-lg py-lg
      bg-white border-b-2 border-[color:var(--frame)]
      md:p-xl md:border-x-2 md:shadow-[inset_3px_0_0_var(--tw-color-stone-200)]
    "
    novalidate
  >
    <!-- Reply target -->
    <input type="hidden" name="parentId" value="" data-reply-input>
    <div
      data-reply-chip
      class="
        flex items-center gap-2 self-start px-2 py-1
        text-sm bg-primary-100 border-2 border-primary-700
      "
      hidden
    >
      <span>Antwort auf <strong data-reply-name></strong></span>
      <button
        type="button"
        data-reply-cancel
        class="inline-flex items-center justify-center min-w-6 min-h-6 text-primary-700"
        aria-label="Antwort verwerfen"
      >
        <span class="i-dinkie-icons-diagonal-cross-small" aria-hidden="true"></span>
      </button>
    </div>

    <!-- Operator mode: the token route reports a logged-in author -->
    <p data-author-note class="text-sm text-contrast-medium" hidden>
      Angemeldet als <strong data-author-name></strong> – deine Antwort erscheint unter diesem Namen.
    </p>

    <div data-name-row class="flex flex-col gap-1">
      <label for="comment-name" class="text-sm font-medium">Name</label>
      <input
        id="comment-name"
        name="name"
        type="text"
        required
        aria-required="true"
        maxlength="60"
        autocomplete="nickname"
        aria-describedby="comment-name-error"
        class="
          px-2 py-1.5 max-w-xs bg-white border-2 border-[color:var(--frame)] transition-colors
          focus-visible:border-primary-700 focus-visible:outline-hidden aria-[invalid]:border-red-600 aria-[invalid]:focus-visible:border-red-600
        "
      >
      <p id="comment-name-error" data-error-for="name" class="text-sm text-red-700" hidden></p>
    </div>

    <div class="flex flex-col gap-1">
      <label for="comment-text" class="text-sm font-medium">Kommentar</label>
      <textarea
        id="comment-text"
        name="text"
        required
        aria-required="true"
        maxlength="4000"
        rows="4"
        aria-describedby="comment-text-error"
        class="
          px-2 py-1.5 resize-y bg-white border-2 border-[color:var(--frame)] transition-colors
          focus-visible:border-primary-700 focus-visible:outline-hidden aria-[invalid]:border-red-600 aria-[invalid]:focus-visible:border-red-600
        "
      ></textarea>
      <p id="comment-text-error" data-error-for="text" class="text-sm text-red-700" hidden></p>
    </div>

    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
      <label for="hp_referrer">Bitte dieses Feld leer lassen</label>
      <input id="hp_referrer" name="hp_referrer" type="text" tabindex="-1" autocomplete="off">
    </div>

    <?php if ($turnstileSitekey): ?>
      <div data-turnstile-mount data-sitekey="<?= esc($turnstileSitekey) ?>"></div>
    <?php endif ?>

    <div class="flex flex-col">
      <!-- aria-live regions: rejects speak through status (red), the accept
           confirmation through success (primary) – only one is ever set. -->
      <p data-form-status role="status" aria-live="polite" class="text-sm text-red-700 [&:not(:empty)]:mb-lg"></p>
      <p data-form-success role="status" aria-live="polite" class="text-sm text-primary-700 [&:not(:empty)]:mb-lg"></p>
      <button type="submit" data-comment-submit class="button-primary self-start">Kommentar absenden</button>
    </div>
  </form>
</div>
