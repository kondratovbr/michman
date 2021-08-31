#!/bin/bash

# {{-- TODO: CRITICAL! Unfinished! The effing manage.py works interactively by default (at least "collectstatic"). Should fix. --}}
# Available environment variables:
# MICHMAN_PROJECT_BRANCH - git branch configured for deployment
# ...

cd /home/{{ $project->serverUsername }}/{{ $project->domain }} || exit

git pull origin "$MICHMAN_PROJECT_BRANCH"

source venv/bin/activate

pip install -r requirements.txt

./manage.py migrate

./manage.py collectstatic
