#!/bin/sh

###############################################################################
# License 
#
# LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
#
# @version 1.0.0
#
###############################################################################
# Note: If you have /bin/dash then change the shebang if /bin/sh does not link 
# to it. Alternativly use /bin/ash
###############################################################################
#
# Helper to merge required externals having the same staging areas: unstable, 
# testing and master
# 
#
# run: ./helper/gitmerge.sh [branchToMergeIntoTheCurrentOne]
#
# Checkout a branch of master|testing|unstable including updates and switching
# to it and including the externals,
# run: ./helper/gitupdate.sh [branchname]
#
# Important note: 
# Staging areas are: "master" "testing" "unstable".
# "master" should be always the latest stable release!
# All new code/ development goes to unstable. Also, if needed to the externals.
# If tests exists and the function of fixed bugs, new features is verified it
# will be merged to "testing" (collecting versions, feature)... the next release
# candidate.
# Hotfixes will go to extra branches and will be merged directly to the "master"
#
# When merging branches take a look into .gitmodules of _each_ branch and 
# verify that the branches still map to the branch name of the master project.
# E.g.: The branch testing of the project maps to the testing branch of the
# submodules eg. library and so on.
###############################################################################

if [ "" = "$1" ]; then
    echo "# run: ./helper/gitmerge.sh [branch:[master|testing|unstable]ToMergeIntoTheCurrentOne]";
    exit 1;
fi

dir=`pwd`;

for mybranch in master testing unstable
do
    if [ "$mybranch" = "$1" ]; then
        echo '+------------------------------------------------------------------------------';
        echo "| check externals, merge $mybranch";

        cmdline="branch=\"\$(git config --file \$toplevel/.gitmodules submodule.\$name.branch)\"; echo \"| merge external: \$name - $mybranch to \$branch\"; cd \"\$toplevel/\$name\" && git merge -m \"gitmerge.sh $mybranch \# to \$branch\" \$mybranch;"

        git submodule foreach --recursive '$cmdline';

#       git submodule foreach --recursive 'branch="$(git config --file $toplevel/.gitmodules \
#           submodule.$name.branch)";\
#           echo "| merge external: $name - $mybranch to $branch";\
#           cd "$toplevel/$name" && git merge -m "gitmerge.sh $mybranch # to $branch" $mybranch;'

    else
        echo "| notting to do for $mybranch";
    fi
done

cd "$dir";

echo "\ndone.\n";


exit;

