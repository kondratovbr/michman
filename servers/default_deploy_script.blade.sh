#!/bin/bash

cd /home/{{ $username }}/{{ $domain }} || exit

git pull origin "$FORGE_SITE_BRANCH"

source venv/bin/activate

pip install -r requirements.txt

./manage.py migrate
./manage.py collectstatic

# {{-- TODO: CRITICAL! Unfinished! --}}
# {{-- TODO: CRITICAL! I need to restart Gunicorn service using systemd here. --}}
