import type * as React from "react";
import { createContext, useContext } from "react";

/**
 * Flipped to true only by the precompile script. It switches `If` from the dev
 * preview (render the children) to build output (emit the markers).
 */
export const PrecompileContext = createContext(false);

/**
 * Precompile: emits a `{#name}…{/name}` block the precompile rewrites into a PHP
 * conditional. Dev preview: renders the children only when `value` is truthy, so
 * the block mirrors its send-time behaviour instead of leaking the raw markers.
 */
export function If({
  name,
  value,
  children,
}: {
  name: string;
  value: unknown;
  children: React.ReactNode;
}) {
  if (useContext(PrecompileContext)) {
    return (
      <>
        {`{#${name}}`}
        {children}
        {`{/${name}}`}
      </>
    );
  }

  return value ? <>{children}</> : null;
}
