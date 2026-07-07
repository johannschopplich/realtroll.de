<?php

$turnstileSitekey = kirby()->option('realtroll.comments.turnstile.sitekey');

?>
<div class="flex flex-col items-start">
  <h3 class="relative z-1 -mb-0.5 px-lg pt-1.5 pb-2 font-heading text-lg leading-none text-primary-700 bg-white border-2 border-b-0 border-primary-300 md:px-xl">Sprich, werter Internetanonymer!</h3>
  <form data-comment-form class="relative flex flex-col gap-lg self-stretch p-lg bg-white border-2 border-primary-300 shadow-[inset_-3px_-3px_0_var(--un-color-primary-100)] md:p-xl" novalidate>
    <!-- Reply target: the TS reveals this chip and fills the hidden `parentId` when
         replying to a top-level comment; the cancel button clears both. -->
    <input type="hidden" name="parentId" value="" data-reply-input>
    <div
      data-reply-chip
      class="flex items-center gap-2 self-start px-2 py-1 text-sm bg-primary-100 border-2 border-primary-700"
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

    <!-- Operator mode: the token route reports a logged-in author, the TS hides
         the name field (its value is set to the author name) and this note shows. -->
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
        class="px-2 py-1.5 max-w-xs bg-theme-background border-2 border-primary-700 aria-[invalid]:border-red-600"
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
        class="px-2 py-1.5 resize-y bg-theme-background border-2 border-primary-700 aria-[invalid]:border-red-600"
      ></textarea>
      <p id="comment-text-error" data-error-for="text" class="text-sm text-red-700" hidden></p>
    </div>

    <!-- Honeypot: an opaque, off-screen decoy. Real visitors never see or fill it;
         a filled value is a hard reject on the server. -->
    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
      <label for="hp_referrer">Bitte dieses Feld leer lassen</label>
      <input id="hp_referrer" name="hp_referrer" type="text" tabindex="-1" autocomplete="off">
    </div>

    <!-- Compact Turnstile mount – min-height reserves the CLS box for the widget
         (~150×140px). The element renders into it explicitly on first expand, or
         removes it in operator mode. -->
    <?php if ($turnstileSitekey): ?>
      <div data-turnstile-mount data-sitekey="<?= esc($turnstileSitekey) ?>" class="min-h-[140px]"></div>
    <?php endif ?>

    <!-- Form-level status: announces `field: 'form'` rejects. Kept in the DOM so
         the live region is present before any message lands. -->
    <p data-form-status role="status" aria-live="polite" class="text-sm text-red-700"></p>

    <button type="submit" data-comment-submit class="button-primary self-start">Kommentar absenden</button>
  </form>
</div>
