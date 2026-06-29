from pathlib import Path

file = Path(r"c:\Users\Administrator\Desktop\Djanproject\resources\views\roles\director\dashboard.blade.php")
content = file.read_text()
idx = content.find("lg:col-span-2 bg-white")
print(repr(content[idx:idx+250]))
