// A project's numbers: the brand against its tracked competitors.
// Visibility is the share of answers that name the brand. Every rate comes with
// its count (k of n) and a 95% interval.
// Usage: SIREFS_API_KEY=sir_... node overview.mjs [project-slug] [days]
import { get, percent, projectSlug } from "./sirefs.mjs";

const slug = await projectSlug();
const days = process.argv[3] ?? 28;
const data = await get(`/projects/${slug}/overview`, { days });

const { start, end } = data.window;
console.log(`${data.project.name}: ${start} to ${end} (${data.window.days} days)`);
console.log();
console.log(
  "Brand".padEnd(16) + "Answers".padStart(8) + "Visibility".padStart(12) + "95% interval".padStart(18) +
    "Cited".padStart(8) + "Recommended".padStart(13) + "Avg rank".padStart(10) + "Share of voice".padStart(16),
);
for (const entity of data.entities) {
  const v = entity.visibility;
  console.log(
    entity.name.padEnd(16) +
      String(entity.answers).padStart(8) +
      percent(v?.rate).padStart(12) +
      (v ? `${percent(v.low)} to ${percent(v.high)}` : "n/a").padStart(18) +
      percent(entity.citation_rate?.rate).padStart(8) +
      percent(entity.recommendation_rate?.rate).padStart(13) +
      (entity.avg_rank == null ? "n/a" : entity.avg_rank.toFixed(2)).padStart(10) +
      percent(entity.share_of_voice).padStart(16),
  );
}

const brand = data.entities.find((entity) => entity.entity === "brand");
console.log();
console.log(`${brand.name} per engine (answers naming it of answers collected):`);
for (const [engine, [named, answers]] of Object.entries(brand.visibility_by_engine)) {
  console.log(`  ${engine.padEnd(16)}${String(named).padStart(4)} of ${answers}`);
}
