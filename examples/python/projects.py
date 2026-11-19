"""List the workspace's projects with the brand's visibility over the last 28 days.

Usage: SIREFS_API_KEY=sir_... python projects.py
"""
from sirefs import get, percent

data = get("/projects")
workspace = data["workspace"]
print(f"Workspace: {workspace['name']} ({workspace['plan']} plan)")

for project in data["projects"]:
    print()
    print(f"{project['name']}  (slug: {project['slug']}, site: {project['domain']})")
    print("  Competitors:   ", ", ".join(project["competitors"]) or "none")
    print("  Engines:       ", ", ".join(project["engines"]))
    print("  Active prompts:", project["prompts_active"])
    v = project["visibility_28d"]
    if v:
        print(f"  Visibility:     {percent(v['rate'])} (named in {v['k']} of {v['n']} answers, "
              f"95% interval {percent(v['low'])} to {percent(v['high'])})")
    else:
        print("  Visibility:     no answers yet")
