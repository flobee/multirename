#!/bin/sh

###############################################################################
# License 
#
# LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
#
# @version 0.3.0
#
###############################################################################
# Helper to update required scripts/ externals
# 
# If you have /bin/dash then change the shebang if /bin/sh does not link to it.
# Alternativly use /bin/ash
#
# run: ./helper/gitupdate.sh # or change paths/locations here
#
# Checkout a branch of master|testing|unstable including updates and switching
# to it and including the externals,
# run: ./helper/gitupdate.sh [branchname]
#
# Important note: Staging areas "master" "testing" "unstable".
# "master" should be always the latest stable release!
# All new code/ development goes to unstable. Also, if needed to the externals.
# If tests exists and the function of fixed bugs, new features is verified it
# will be merged to "testing" (collecting versions, feature)... hte next release
# candidate.
# Hotfixes will go extra branches and will be merged directly to the "master"
#
# When merging branches take a look into .gitmodules of each! branch and verify
# that the branches still map to the branch name of the master project.
# E.g.: The branch testing of the project maps to the testing branch of the
# submodules eg. library and so on.
###############################################################################

for mybranch in master testing unstable
do
    if [ "$mybranch" = "$1" ]; then
        git checkout $mybranch;
        echo '+------------------------------------------------------------------------------';
        echo "| checkout branch $mybranch";

        ln -sf .gitmodules_$mybranch .gitmodules
        echo '+------------------------------------------------------------------------------';
        echo "| link .gitmodules_$mybranch TO .gitmodules";
    fi
done


echo '+------------------------------------------------------------------------------';
echo '| update base program';
git pull --all;

if [ ! -d externals/mumsys-library ]; then
    echo '+------------------------------------------------------------------------------';
    echo '| adds core library as submodule in "externals/mumsys-library"';
    git submodule add https://github.com/flobee/mumsys-library.git externals/mumsys-library
fi

if [ ! -d externals/multirename.wiki ]; then
    echo '+------------------------------------------------------------------------------';
    echo '| adds wiki docs as submodule in "externals/multirename.wiki"';
    git submodule add https://github.com/flobee/multirename.wiki.git externals/multirename.wiki
fi

if [ ! -f externals/mumsys-library/.git ]; then
    echo '+------------------------------------------------------------------------------';
    echo '| init submodules';
    git submodule update --init --recursive;
#else
    # echo '+------------------------------------------------------------------------------';
    # echo '| update submodules';
    # git submodule update --remote; # --recursive;
fi

echo '+------------------------------------------------------------------------------';
echo '| switching branch of submodules if not already done (checks: .gitmodules)';
git submodule foreach --recursive 'branch="$(git config -f $toplevel/.gitmodules \
submodule.$name.branch)";\
echo "| checking $branch";\
git checkout $branch;\
echo "| pull updates";\
git pull;'

echo "\ndone.\n";


exit;

