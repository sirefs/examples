"""The domains and pages the engines cite when answering the project's prompts.

`owner` says whose site it is: own, competitor or third_party. A third-party
site cited often is a place to get mentioned.

Usage: SIREFS_API_KEY=sir_... python sources.py [project-slug] [days]
"""
import sys

from sirefs import get, project_slug

slug = project_slug()
days = int(sys.argv[2]) if len(sys.argv) > 2 else 28
data = get(f"/projects/{slug}/sources", {"days": days})

print(f"Cited sources across {data['answers']} answers")
print()
print(f"{'Answers':>8}  {'Owner':<12}{'Engines':>8}  Domain")
for domain in data["domains"][:15]:
    print(f"{domain['answers']:>8}  {domain['owner']:<12}{len(domain['engines']):>8}  {domain['domain']}")

print()
print("Most cited pages:")
for page in data["pages"][:10]:
    print(f"{page['answers']:>8}  {page['owner']:<12}{page['url']}")
