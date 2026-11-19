// The project's prompts (the questions asked of the engines) with their results.
// For each prompt: how many answers name the brand, in total and per engine.
// Prompts where the brand is never named are where to act.
// Usage: SIREFS_API_KEY=sir_... node prompts.mjs [project-slug] [days]
import { get, projectSlug } from "./sirefs.mjs";

const slug = await projectSlug();
const days = process.argv[3] ?? 28;
const data = await get(`/projects/${slug}/prompts`, { days });

const share = (prompt) => prompt.brand_named[0] / Math.max(prompt.brand_named[1], 1);
console.log(`Prompts, ${data.window.start} to ${data.window.end}. Brand named in k of n answers.`);
for (const prompt of [...data.prompts].sort((a, b) => share(a) - share(b))) {
  const [named, answers] = prompt.brand_named;
  const status = prompt.active ? "" : "  (paused)";
  console.log();
  console.log(
    `${String(named).padStart(3)} of ${String(answers).padEnd(4)} ${prompt.text}  [${prompt.market}, ${prompt.topic ?? "no topic"}]${status}`,
  );
  const perEngine = Object.entries(prompt.by_engine).map(([engine, [k, n]]) => `${engine} ${k}/${n}`).join(", ");
  if (perEngine) console.log(`            ${perEngine}`);
}
