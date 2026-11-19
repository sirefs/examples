"""A project's numbers: the brand against its tracked competitors.

Visibility is the share of answers that name the brand. Every rate comes with
its count (k of n) and a 95% interval.

Usage: SIREFS_API_KEY=sir_... python overview.py [project-slug] [days]
"""
import sys

from sirefs import get, percent, project_slug

slug = project_slug()
days = int(sys.argv[2]) if len(sys.argv) > 2 else 28
data = get(f"/projects/{slug}/overview", {"days": days})

window = data["window"]
print(f"{data['project']['name']}: {window['start']} to {window['end']} ({window['days']} days)")
print()
print(f"{'Brand':<16}{'Answers':>8}{'Visibility':>12}{'95% interval':>18}{'Cited':>8}{'Recommended':>13}{'Avg rank':>10}{'Share of voice':>16}")
for entity in data["entities"]:
    v = entity["visibility"] or {}
    interval = f"{percent(v.get('low'))} to {percent(v.get('high'))}" if v else "n/a"
    rank = entity["avg_rank"]
    print(f"{entity['name']:<16}{entity['answers']:>8}{percent(v.get('rate')):>12}{interval:>18}"
          f"{percent((entity['citation_rate'] or {}).get('rate')):>8}"
          f"{percent((entity['recommendation_rate'] or {}).get('rate')):>13}"
          f"{(f'{rank:.2f}' if rank is not None else 'n/a'):>10}"
          f"{percent(entity['share_of_voice']):>16}")

brand = next(e for e in data["entities"] if e["entity"] == "brand")
print()
print(f"{brand['name']} per engine (answers naming it of answers collected):")
for engine, (named, answers) in brand["visibility_by_engine"].items():
    print(f"  {engine:<16}{named:>4} of {answers}")
