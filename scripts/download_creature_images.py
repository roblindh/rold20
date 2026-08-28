import re
import os
import sys
import time
import urllib.request
import urllib.error

def extract_creatures(sql_path):
    with open(sql_path, "r", encoding="utf-8", errors="ignore") as f:
        sql = f.read()

    start_idx = sql.find("INSERT INTO `creatures` VALUES ")
    end_idx = sql.find("UNLOCK TABLES;", start_idx)
    creatures_sql = sql[start_idx:end_idx]

    pattern = re.compile(r"\((\d+),'([^']+)',.*?,(NULL|'[^']*'),(NULL|'[^']*')\)")
    results = []
    for m in pattern.finditer(creatures_sql):
        cid = int(m.group(1))
        name = m.group(2)
        img_url = m.group(4).strip("'") if m.group(4) != "NULL" else None
        if img_url:
            results.append((cid, name, img_url))
    return results

def resolve_url(raw_url):
    url = raw_url.strip()
    if "wizards.com/dnd/images/" in url:
        if url.startswith("https://"):
            url = "http://" + url[8:]
        return f"https://web.archive.org/web/20160401000000im_/{url}"
    elif "vignette.wikia.nocookie.net" in url or "static.wikia.nocookie.net" in url:
        url = url.replace("vignette.wikia.nocookie.net", "static.wikia.nocookie.net")
        url = re.sub(r"/revision/latest.*$", "", url)
        url = re.sub(r"/scale-to-width-down/\d+", "", url)
        return url
    return url

def get_extension(url, content_type):
    if "png" in content_type.lower() or url.lower().endswith(".png"):
        return ".png"
    elif "webp" in content_type.lower() or url.lower().endswith(".webp"):
        return ".webp"
    elif "gif" in content_type.lower() or url.lower().endswith(".gif"):
        return ".gif"
    return ".jpg"

def download_images(creatures, output_dir, max_count=None, force=False):
    os.makedirs(output_dir, exist_ok=True)
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
    
    total = len(creatures) if max_count is None else min(len(creatures), max_count)
    succeeded = 0
    skipped = 0
    failed = 0

    print(f"Starting download for {total} creature images into '{output_dir}'...\n")

    for idx, (cid, name, raw_url) in enumerate(creatures[:total], 1):
        # Check existing
        existing = [f for f in os.listdir(output_dir) if f.startswith(f"{cid}.") and os.path.getsize(os.path.join(output_dir, f)) > 500]
        if existing and not force:
            print(f"[{idx}/{total}] ID {cid:3d} ({name}): Already exists ({existing[0]}), skipping.")
            skipped += 1
            continue

        resolved = resolve_url(raw_url)
        retries = 4
        success = False

        for attempt in range(retries):
            try:
                req = urllib.request.Request(resolved, headers=headers)
                with urllib.request.urlopen(req, timeout=15) as resp:
                    if resp.status == 200:
                        data = resp.read()
                        if len(data) > 500:
                            ctype = resp.headers.get("Content-Type", "")
                            ext = get_extension(resolved, ctype)
                            dest = os.path.join(output_dir, f"{cid}{ext}")
                            with open(dest, "wb") as out_f:
                                out_f.write(data)
                            print(f"[{idx}/{total}] ID {cid:3d} ({name}): Downloaded {len(data):,} bytes -> {cid}{ext}")
                            succeeded += 1
                            success = True
                            break
            except Exception as e:
                backoff = 2.5 * (attempt + 1)
                if attempt < retries - 1:
                    time.sleep(backoff)
                else:
                    print(f"[{idx}/{total}] ID {cid:3d} ({name}): FAILED -> {e}")

        if not success:
            failed += 1

        time.sleep(0.8)

    print(f"\nCompleted: {succeeded} downloaded, {skipped} skipped, {failed} failed.")

if __name__ == "__main__":
    script_dir = os.path.dirname(os.path.abspath(__file__))
    root_dir = os.path.dirname(script_dir)
    sql_path = os.path.join(root_dir, "dbdump", "Dump20200708.sql")
    out_dir = os.path.join(root_dir, "images", "creatures")

    creatures = extract_creatures(sql_path)
    # Check arguments
    max_c = int(sys.argv[1]) if len(sys.argv) > 1 else None
    download_images(creatures, out_dir, max_count=max_c)
