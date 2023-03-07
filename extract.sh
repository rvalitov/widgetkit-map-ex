#!/bin/bash
#This script extracts all the strings from Yootheme plugin's folder and saves them in json format for translation.
#Author: Ramil Valitov, ramil@walitoff.com, https://walitoff.com

#Script vars
S_SED=$(which sed)
S_PERL=$(which perl)
S_INPUT="$1"
S_OUTPUT="$2"

cat <<EOF
Usage: $0 plugin_folder output.json

This script extracts all the strings from Yootheme plugin's folder and saves them in json format for translation.
Author: Ramil Valitov, ramil@walitoff.com, https://walitoff.com
EOF

if [[ -z $S_INPUT ]]; then
	echo "Input folder not specified"
	exit 1
fi

if [[ ! -d $S_INPUT ]]; then
	echo "Specified input directory not found"
	exit 1
fi

if [[ -z $S_OUTPUT ]]; then
	echo "Output file not specified"
	exit 1
fi

#Find all PHP files
FILES_LIST=$(find "$S_INPUT" -type f -name "*.php")

while read -r phpfile; do
	# Analyze each PHP file

  # We use perl, because:
  # grep -P may give error: exceeded PCRE's backtracking limit
  # sed does not support PCRE
  # awk does not support PCRE

	# First scan is for standard {{ 'ABC' |trans}} strings
	# Examples:
	# <label class="uk-form-label">{{'Scale Controls' | trans}} <span uk-icon="icon: info" uk-tooltip data-title="{{'Enables/disables the Scale control that provides a simple map scale.'|trans}}"></span></label>
	# <input id="wk-styler-lightness" class="uk-input uk-form-width-small" type="text" ng-model="widget.data['styler_lightness']"> ({{'%from% to %to%' | trans: {from:-100, to:100} }})
	# Regexp:
	# (?<={{(?>'|"))(?<result>[^}]+)(?=(?>'|")\s*\|\s*trans(?>:\s+{[\w\s+\-\.\,%\:]+})?\s*}})
	# https://regex101.com/r/ENGVan/1/
	#LIST=$($S_PERL -wln -e "/(?<={{(?>'|\"))(?<result>[^}]+)(?=(?>'|\")\s*\|\s*trans(?>:\s+{[\w\s+\-\.\,%\:]+})?\s*}})/ and print $&;" "$phpfile")
	#STRING_LIST=$(echo -e "$STRING_LIST"; echo -e "$LIST")

	# Second scan is for PHP invoked calls like $app['translator']->trans('ABC')
	# Examples:
	# $app['translator']->trans($settings['link_text']);
	# $appWK['translator']->trans('Loading, please wait...');
	# $appWK['translator']->trans('New release of plugin %name% is available!', array('%name%' => $settings['name']));
	# $appWK['translator']->trans('Downloaded information about %number% items.');
	# Regexp:
	# (?<=->trans\(')((?:(?:[^']|\\')*)+)(?='\s*(?>\)|,))|(?<=->trans\(")((?:(?:[^"]|\\")*)+)(?="\s*(?>\)|,))
	# It's based on 2 parts - one for single quote:
	# (?<=->trans\(')((?:(?:[^']|\\')*)+)(?='\s*(?>\)|,))
	# Second for double quote:
	# (?<=->trans\(")((?:(?:[^"]|\\")*)+)(?="\s*(?>\)|,))
	# https://regex101.com/r/XzPXm8/1
	LIST=$($S_PERL -wln -e "/(?<=->trans\(')((?:(?:[^']|\\'))+)(?='(?:\)|,))/ and print $&;" "$phpfile")
	STRING_LIST=$(echo -e "$STRING_LIST"; echo -e "$LIST")
	echo "$LIST"
done <<< "$FILES_LIST"

#Removing duplicate lines and sorting in alphabetical order, removing empty lines, escaping symbols for JSON:
STRING_LIST=$(echo -e -n "$STRING_LIST" | $S_SED -e '/^\s*$/d' | $S_SED -e 's/"/\\\\"/g' | sort -u)

pos=0;
echo "{" > "$S_OUTPUT"
while read -r line; do
	if [[ $pos -gt 0 ]]; then
		echo -e "," >> "$S_OUTPUT"
	fi
    echo -e -n "\t\"$line\": \"$line\"" >> "$S_OUTPUT"
	pos=1
done <<< "$STRING_LIST"
echo >> "$S_OUTPUT"
echo -n "}" >> "$S_OUTPUT"

STRING_COUNT=$(echo -e "$STRING_LIST" | wc -l)
echo "Extraction complete, $STRING_COUNT phrases found"
exit 0
