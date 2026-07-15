import { ofetch } from "ofetch";
import { upgradeRelativeTimestamps } from "../utils/index.ts";

const TURNSTILE_SCRIPT =
  "https://challenges.cloudflare.com/turnstile/v0/api.js";

interface TokenResponse {
  csrf: string;
}

interface SubmitResult {
  ok: boolean;
  html?: string;
  anchor?: string;
  field?: string;
  code?: string;
  message?: string;
}

interface TurnstileRenderOptions {
  sitekey: string;
  size?: string;
  retry?: "auto" | "never";
  action?: string;
  callback?: () => void;
  "error-callback"?: (code?: string) => boolean | void;
}

declare global {
  interface Window {
    turnstile?: {
      render: (
        el: HTMLElement,
        options: TurnstileRenderOptions,
      ) => string | undefined;
      getResponse: (widgetId?: string) => string | undefined;
      reset: (widgetId?: string) => void;
    };
    onTurnstileLoad?: () => void;
  }
}

export class CommentSection extends HTMLElement {
  #form!: HTMLFormElement;
  #textArea!: HTMLTextAreaElement;
  #parentInput: HTMLInputElement | null = null;
  #submitButton: HTMLButtonElement | null = null;
  #submitLabel = "";
  #statusEl: HTMLElement | null = null;
  #successEl: HTMLElement | null = null;
  #nameRow: HTMLElement | null = null;
  #replyChip: HTMLElement | null = null;
  #replyNameEl: HTMLElement | null = null;
  #csrf: string | undefined;
  #mention = "";
  #turnstileRequested = false;
  #turnstileWidgetId: string | undefined;
  #turnstileRetryCount = 0;

  connectedCallback() {
    upgradeRelativeTimestamps(this);

    // On reload the browser restores the scroll offset instead of resolving
    // the fragment, so target the fresh comment explicitly.
    const { hash } = location;
    if (hash.startsWith("#kommentar")) {
      document
        .getElementById(decodeURIComponent(hash.slice(1)))
        ?.scrollIntoView();
    }

    const form = this.querySelector<HTMLFormElement>("[data-comment-form]");
    const nameInput = form?.querySelector<HTMLInputElement>('[name="name"]');
    const textArea = form?.querySelector<HTMLTextAreaElement>('[name="text"]');
    if (!form || !nameInput || !textArea) return;

    this.#form = form;
    this.#textArea = textArea;
    this.#parentInput = form.querySelector("[data-reply-input]");
    this.#submitButton = form.querySelector("[data-comment-submit]");
    this.#submitLabel = this.#submitButton?.textContent?.trim() ?? "";
    this.#statusEl = form.querySelector("[data-form-status]");
    this.#successEl = form.querySelector("[data-form-success]");
    this.#nameRow = form.querySelector("[data-name-row]");
    this.#replyChip = form.querySelector("[data-reply-chip]");
    this.#replyNameEl = form.querySelector("[data-reply-name]");

    // A visitor who only reads never triggers a session cookie or a Cloudflare
    // request; focusin bubbles from every field, so the first touch sets up once.
    form.addEventListener("focusin", this.#onFirstInteraction, { once: true });
    this.addEventListener("click", this.#onReplyClick);
    form
      .querySelector("[data-reply-cancel]")
      ?.addEventListener("click", this.#onReplyCancel);
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      void this.#handleSubmit();
    });
  }

  #onFirstInteraction = () => {
    void this.#fetchToken().then((token) => {
      if (token) this.#csrf = token.csrf;
      this.#loadTurnstile();
    });
  };

  #onReplyClick = (event: Event) => {
    const replyButton = (event.target as HTMLElement).closest<HTMLElement>(
      "[data-reply-to]",
    );
    if (!replyButton) return;

    const replyName = replyButton.dataset.replyName ?? "";
    if (this.#parentInput)
      this.#parentInput.value = replyButton.dataset.replyTo ?? "";
    if (this.#replyChip) this.#replyChip.hidden = false;
    if (this.#replyNameEl) this.#replyNameEl.textContent = replyName;

    this.#stripMention();
    if (replyName && replyButton.dataset.replyNested !== undefined) {
      this.#mention = `@${replyName} `;
      this.#textArea.value = this.#mention + this.#textArea.value;
    }

    this.#syncSubmitLabel();
    this.#textArea.focus();
    const caret = this.#textArea.value.length;
    this.#textArea.setSelectionRange(caret, caret);
  };

  #onReplyCancel = () => {
    if (this.#parentInput) this.#parentInput.value = "";
    if (this.#replyChip) this.#replyChip.hidden = true;
    this.#stripMention();
    this.#syncSubmitLabel();
  };

  #stripMention() {
    if (this.#mention && this.#textArea.value.startsWith(this.#mention)) {
      this.#textArea.value = this.#textArea.value.slice(this.#mention.length);
    }
    this.#mention = "";
  }

  #syncSubmitLabel() {
    if (!this.#submitButton || this.#submitButton.disabled) return;
    this.#submitButton.textContent = this.#parentInput?.value
      ? "Antwort absenden"
      : this.#submitLabel;
  }

  async #fetchToken(): Promise<TokenResponse | undefined> {
    try {
      return await ofetch<TokenResponse>(this.dataset.tokenUrl ?? "", {
        headers: { Accept: "application/json" },
      });
    } catch {}
  }

  // Explicit rendering captures the widget id for getResponse/reset; the
  // implicit scan only works if the widget is visible when the script loads.
  #loadTurnstile() {
    if (this.#turnstileRequested) return;
    const mount = this.#form.querySelector<HTMLElement>(
      "[data-turnstile-mount]",
    );
    if (!mount) return;
    this.#turnstileRequested = true;

    // api.js is lazy-loaded, so the onload callback must exist before injection.
    window.onTurnstileLoad = () => {
      this.#turnstileWidgetId = window.turnstile?.render(mount, {
        sitekey: mount.dataset.sitekey ?? "",
        action: "comment",
        size: "compact",
        retry: "auto",
        // When the challenge passes, reset the counter so a later error gets
        // a fresh two retries.
        callback: () => {
          this.#turnstileRetryCount = 0;
        },
        // First two errors: return false to defer to Turnstile's own retry
        // backoff. After that: show a message and return true. The callback
        // never re-renders the widget, so a persistent error can't blink-loop.
        "error-callback": () => {
          if (++this.#turnstileRetryCount <= 2) return false;
          if (this.#statusEl) {
            this.#statusEl.textContent =
              "Die Sicherheitsprüfung konnte nicht geladen werden. Bitte lade die Seite neu und versuche es erneut.";
          }
          return true;
        },
      });
    };

    const script = document.createElement("script");
    script.src = `${TURNSTILE_SCRIPT}?render=explicit&onload=onTurnstileLoad`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  }

  async #handleSubmit() {
    this.#resetFeedback();
    this.#setPending(true);

    try {
      if (!this.#csrf) {
        const token = await this.#fetchToken();
        if (token) this.#csrf = token.csrf;
      }

      let result = await this.#send();

      // Transparent CSRF retry: a stale session token is a pure token swap –
      // re-fetch once and resend the same submission, no other side effect.
      if (!result.ok && result.code === "csrf") {
        this.#csrf = undefined;
        const token = await this.#fetchToken();
        if (token) this.#csrf = token.csrf;
        result = await this.#send();
      }

      if (result.ok && result.html) {
        this.#applySuccess(result.html, result.anchor);
        return;
      }

      this.#showReject(result);
    } finally {
      this.#setPending(false);
    }
  }

  #setPending(isPending: boolean) {
    if (!this.#submitButton) return;
    this.#submitButton.disabled = isPending;
    if (isPending) {
      this.#submitButton.textContent = "Wird gesendet …";
    } else {
      this.#syncSubmitLabel();
    }
  }

  // Swap the re-rendered thread in place of the current one, then confirm and
  // scroll to the new comment.
  #applySuccess(html: string, anchor?: string) {
    replaceThread(this, html);

    upgradeRelativeTimestamps(this);

    this.#textArea.value = "";
    this.#onReplyCancel();
    window.turnstile?.reset(this.#turnstileWidgetId);
    if (this.#successEl)
      this.#successEl.textContent = "Danke! Dein Kommentar ist jetzt online.";

    if (anchor) {
      document.getElementById(anchor)?.scrollIntoView({ behavior: "smooth" });
    }
  }

  async #send(): Promise<SubmitResult> {
    const body = toUrlEncoded(this.#form, {
      pageUuid: this.dataset.pageUuid ?? "",
      csrf: this.#csrf ?? "",
    });
    // Fall back to the widget's token only if the form didn't already carry one.
    if (!body.get("cf-turnstile-response")) {
      body.set(
        "cf-turnstile-response",
        window.turnstile?.getResponse(this.#turnstileWidgetId) ?? "",
      );
    }

    try {
      const response = await ofetch.raw<SubmitResult>(
        this.dataset.submitUrl ?? "",
        {
          method: "POST",
          headers: { Accept: "application/json" },
          body,
          ignoreResponseError: true,
        },
      );
      const data = response._data ?? ({} as SubmitResult);

      return response.ok
        ? { ok: true, html: data.html, anchor: data.anchor }
        : {
            ok: false,
            field: data.field,
            code: data.code,
            message: data.message,
          };
    } catch {
      return {
        ok: false,
        field: "form",
        message: "Verbindung fehlgeschlagen. Bitte versuche es erneut.",
      };
    }
  }

  #showReject(result: SubmitResult) {
    const message =
      result.message ??
      "Es ist ein Fehler aufgetreten. Bitte versuche es erneut.";

    // A field error focuses that field and shows its inline message; a form
    // error speaks through the live region instead – never both for one event.
    // The exception: operator mode hides the name row, so a name error would
    // land on an invisible, unfocusable input – route it to the live region.
    const nameRowHidden =
      result.field === "name" && this.#nameRow?.hidden === true;
    if (
      (result.field === "name" || result.field === "text") &&
      !nameRowHidden
    ) {
      const input = this.#form.querySelector<HTMLElement>(
        `[name="${result.field}"]`,
      );
      const errorEl = this.#form.querySelector<HTMLElement>(
        `[data-error-for="${result.field}"]`,
      );
      input?.setAttribute("aria-invalid", "true");
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.hidden = false;
      }
      input?.focus();
      return;
    }

    if (this.#statusEl) this.#statusEl.textContent = message;
    if (result.code === "turnstile")
      window.turnstile?.reset(this.#turnstileWidgetId);
  }

  #resetFeedback() {
    if (this.#statusEl) this.#statusEl.textContent = "";
    if (this.#successEl) this.#successEl.textContent = "";
    for (const input of this.#form.querySelectorAll("[aria-invalid]")) {
      input.removeAttribute("aria-invalid");
    }
    for (const errorEl of this.#form.querySelectorAll<HTMLElement>(
      "[data-error-for]",
    )) {
      errorEl.textContent = "";
      errorEl.hidden = true;
    }
  }
}

function toUrlEncoded(
  form: HTMLFormElement,
  overrides: Record<string, string>,
): URLSearchParams {
  const body = new URLSearchParams();

  for (const [key, value] of new FormData(form)) {
    body.append(key, typeof value === "string" ? value : "");
  }

  for (const [key, value] of Object.entries(overrides)) {
    body.set(key, value);
  }

  return body;
}

// Replace the current comment thread with a server-rendered one. The snippet
// recomputes reply nesting server-side, so swapping the whole thread keeps
// threading and the count correct where inserting a single node would not.
function replaceThread(root: ParentNode, html: string) {
  const current = root.querySelector("[data-comment-thread]");
  const next = document
    .createRange()
    .createContextualFragment(html)
    .querySelector("[data-comment-thread]");

  if (current && next) current.replaceWith(next);
}

export function setup() {
  customElements.define("comment-section", CommentSection);
}
