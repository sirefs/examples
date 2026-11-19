// The domains and pages the engines cite when answering the project's prompts.
// `owner` says whose site it is: own, competitor or third_party. A third-party
// site cited often is a place to get mentioned.
// Usage: SIREFS_API_KEY=sir_... node sources.mjs [project-slug] [days]
import { get, projectSlug } from "./sirefs.mjs";

const slug = await projectSlug();
const days = process.argv[3] ?? 28;
const data = await get(`/projects/${slug}/sources`, { days });

console.log(`Cited sources across ${data.answers} answers`);
console.log();
console.log(`${"Answers".padStart(8)}  ${"Owner".padEnd(12)}${"Engines".padStart(8)}  Domain`);
for (const domain of data.domains.slice(0, 15)) {
  console.log(
    `${String(domain.answers).padStart(8)}  ${domain.owner.padEnd(12)}${String(domain.engines.length).padStart(8)}  ${domain.domain}`,
  );
}

console.log();
console.log("Most cited pages:");
for (const page of data.pages.slice(0, 10)) {
  console.log(`${String(page.answers).padStart(8)}  ${page.owner.padEnd(12)}${page.url}`);
}
