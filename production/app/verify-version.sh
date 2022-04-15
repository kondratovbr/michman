#!/bin/sh
set -e

if [ -z "${APP_ROOT}" ]; then
    echo 'The verify-version.sh script requires an APP_ROOT env variable.';
    exit 1;
fi

if [ -z "${APP_VERSION}" ]; then
    echo 'The verify-version.sh script requires an APP_VERSION env variable. Which will be compared to the output of "artisan version"';
    exit 1;
fi

version=$(php "$APP_ROOT"/artisan version);

if [ "$version" != "$APP_VERSION" ]; then
    >&2 echo "ERROR: Requested version ($APP_VERSION) is different from the version of sources provided ($version)";
    exit 1;
else
    echo "App version verified: $version";
fi

exit 0;
