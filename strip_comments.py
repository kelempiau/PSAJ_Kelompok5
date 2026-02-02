import os
import re

def strip_comments(text, ext):
    if ext in ['.php', '.js', '.css']:
        # Strip multi-line comments
        text = re.sub(r'/\*.*?\*/', '', text, flags=re.DOTALL)
        # Strip single-line comments (be careful with URLs)
        # This regex avoids stripping // if preceded by : (like in http://)
        text = re.sub(r'(?<!:)\/\/.*', '', text)
        if ext == '.php':
            # Strip bash style comments in PHP
            text = re.sub(r'#.*', '', text)
    if ext in ['.php', '.html']:
        # Strip HTML comments
        text = re.sub(r'<!--.*?-->', '', text, flags=re.DOTALL)
    if ext == '.sql':
        # Strip SQL comments
        text = re.sub(r'--.*', '', text)
    return text

root_dir = r"d:\web reservasi"
extensions = ['.php', '.js', '.sql', '.html', '.css']

for root, dirs, files in os.walk(root_dir):
    if '.git' in dirs:
        dirs.remove('.git')
    for file in files:
        if any(file.endswith(ext) for ext in extensions):
            path = os.path.join(root, file)
            with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            ext = os.path.splitext(file)[1]
            new_content = strip_comments(content, ext)
            
            if new_content != content:
                with open(path, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print(f"Cleaned: {path}")
