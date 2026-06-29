import re
from pathlib import Path

dashboards = Path(r"c:\Users\Administrator\Desktop\Djanproject\resources\views\roles").rglob("dashboard.blade.php")

for file in dashboards:
    with open(file, "r", newline="") as f:
        content = f.read()

    # Remove AI insights include from inside the chart column
    content = re.sub(
        r'\n    <div class="lg:col-span-2 bg-white rounded-xl border p-5">\n\n@include\(\'roles\.shared\.ai-insights\'\)\n\n',
        '\n    <div class="lg:col-span-2 bg-white rounded-xl border p-5">\n',
        content,
    )

    with open(file, "w", newline="") as f:
        f.write(content)

print("Dashboards fixed")
