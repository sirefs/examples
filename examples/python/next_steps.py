"""What to do about the numbers, ranked by impact, each step with its evidence.

Usage: SIREFS_API_KEY=sir_... python next_steps.py [project-slug] [days]
"""
import sys

from sirefs import get, project_slug

slug = project_slug()
days = int(sys.argv[2]) if len(sys.argv) > 2 else 28
data = get(f"/projects/{slug}/next-steps", {"days": days})

if not data["steps"]:
    print("No open steps.")
for number, step in enumerate(data["steps"], 1):
    print(f"{number}. [{step['impact']}] {step['title']}")
    print(f"   {step['detail']}")
    print(f"   kind: {step['kind']}, id: {step['id']}")
    if step.get("link"):
        print(f"   {step['link']['label']}: {step['link']['href']}")
    print()
