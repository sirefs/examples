// What to do about the numbers, ranked by impact, each step with its evidence.
// Usage: SIREFS_API_KEY=sir_... node next-steps.mjs [project-slug] [days]
import { get, projectSlug } from "./sirefs.mjs";

const slug = await projectSlug();
const days = process.argv[3] ?? 28;
const data = await get(`/projects/${slug}/next-steps`, { days });

if (!data.steps.length) console.log("No open steps.");
data.steps.forEach((step, index) => {
  console.log(`${index + 1}. [${step.impact}] ${step.title}`);
  console.log(`   ${step.detail}`);
  console.log(`   kind: ${step.kind}, id: ${step.id}`);
  if (step.link) console.log(`   ${step.link.label}: ${step.link.href}`);
  console.log();
});
