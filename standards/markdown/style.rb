all
exclude_rule 'MD001' # Header levels should only increment by one level at a time: won't fix
exclude_rule 'MD002' # First header should be a top level header: won't fix
exclude_rule 'MD005' # Inconsistent indentation for list items at the same level: bug
exclude_rule 'MD007' # Unordered list indentation: bug
rule 'MD009', :br_spaces => 2 # Trailing spaces
rule 'MD013', :line_length => 120 # Line length
exclude_rule 'MD024' # Multiple headers with the same content: won't fix
exclude_rule 'MD025' # Multiple top level headers in the same document: won't fix
exclude_rule 'MD026' # Trailing punctuation in header: won't fix
exclude_rule 'MD041' # First line in file should be a top level header: won't fix
