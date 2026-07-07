// Replace each absolute German timestamp with a relative one, keeping the
// absolute value as the title/fallback.
export function upgradeRelativeTimestamps(root: ParentNode): void {
  const formatter = new Intl.RelativeTimeFormat("de", { numeric: "auto" });
  const units: [Intl.RelativeTimeFormatUnit, number][] = [
    ["year", 31536000],
    ["month", 2592000],
    ["week", 604800],
    ["day", 86400],
    ["hour", 3600],
    ["minute", 60],
    ["second", 1],
  ];

  for (const time of root.querySelectorAll<HTMLTimeElement>("time[datetime]")) {
    const iso = time.getAttribute("datetime");
    if (!iso) continue;
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) continue;

    const seconds = Math.round((date.getTime() - Date.now()) / 1000);
    for (const [unit, secondsPerUnit] of units) {
      if (Math.abs(seconds) >= secondsPerUnit || unit === "second") {
        time.title = time.textContent ?? "";
        time.textContent = formatter.format(
          Math.round(seconds / secondsPerUnit),
          unit,
        );
        break;
      }
    }
  }
}
