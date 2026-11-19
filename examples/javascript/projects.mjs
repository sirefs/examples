// List the workspace's projects with the brand's visibility over the last 28 days.
// Usage: SIREFS_API_KEY=sir_... node projects.mjs
import { get, percent } from "./sirefs.mjs";

const { workspace, projects } = await get("/projects");
console.log(`Workspace: ${workspace.name} (${workspace.plan} plan)`);

for (const project of projects) {
  console.log();
  console.log(`${project.name}  (slug: ${project.slug}, site: ${project.domain})`);
  console.log("  Competitors:   ", project.competitors.join(", ") || "none");
  console.log("  Engines:       ", project.engines.join(", "));
  console.log("  Active prompts:", project.prompts_active);
  const v = project.visibility_28d;
  console.log(
    v
      ? `  Visibility:     ${percent(v.rate)} (named in ${v.k} of ${v.n} answers, 95% interval ${percent(v.low)} to ${percent(v.high)})`
      : "  Visibility:     no answers yet",
  );
}
