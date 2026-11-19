"""The project's prompts (the questions asked of the engines) with their results.

For each prompt: how many answers name the brand, in total and per engine.
Prompts where the brand is never named are where to act.

Usage: SIREFS_API_KEY=sir_... python prompts.py [project-slug] [days]
"""
import sys

from sirefs import get, project_slug

slug = project_slug()
days = int(sys.argv[2]) if len(sys.argv) > 2 else 28
data = get(f"/projects/{slug}/prompts", {"days": days})

window = data["window"]
print(f"Prompts, {window['start']} to {window['end']}. Brand named in k of n answers.")
for prompt in sorted(data["prompts"], key=lambda p: p["brand_named"][0] / max(p["brand_named"][1], 1)):
    named, answers = prompt["brand_named"]
    status = "" if prompt["active"] else "  (paused)"
    print()
    print(f"{named:>3} of {answers:<4} {prompt['text']}  [{prompt['market']}, {prompt['topic'] or 'no topic'}]{status}")
    per_engine = ", ".join(f"{engine} {k}/{n}" for engine, (k, n) in prompt["by_engine"].items())
    if per_engine:
        print(f"            {per_engine}")
