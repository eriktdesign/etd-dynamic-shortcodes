=== ETD Dynamic Shortcodes ===
Contributors: eriktdesign
Tags: shortcode, text replacement

Create your own text-replacement shortcodes.

== Description ==
Do you have small pieces of information that appear across your website, but need to be periodically updated? Use this plugin to create your own text-replacement shortcodes to keep small bits of content up to date.

Example: Perhaps your run a Real Estate website and display current interest rates in various places on your website. Create a `[currentrate]` shortcode and use it wherever you reference the interest rate. Then whenever the rate changes, update it in one place and it\'s updated everywhere. 

== Frequently Asked Questions ==
Case Conversion
Pass a `case` argument to your shortcode to change the letter case of the output. For example, `[foo case=upper]` will output the value of your shortcode in uppercase. Valid cases are `upper`, `lower`, `first`, and `words`.

Filter
Output of the shortcodes is filtered through the `dynamic_shortcode_$tag` filter. (eg, `dynamic_shortcode_foo`).

== Screenshots ==
1. Editor screen for creating a shortcode