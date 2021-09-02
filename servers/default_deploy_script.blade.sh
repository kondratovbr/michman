#!/bin/bash

# Available environment variables:
# MICHMAN_PROJECT_BRANCH - git branch configured for deployment
# ...

cd /home/{{ $project->serverUsername }}/{{ $project->domain }} || exit

git pull origin "$MICHMAN_PROJECT_BRANCH"

source venv/bin/activate

pip install -r requirements.txt

./manage.py migrate --noinput

./manage.py collectstatic --noinput
