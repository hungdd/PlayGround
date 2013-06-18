=== WP Ajax Edit Comments ===
Contributors: Ajay, ronalfy
Tags: ajax, comments,move comments,blacklist, blacklist comments,edit comments, edit, comment, admin
Requires at least: 2.8
Tested up to: 3.2
Stable tag: 3.2.1.0

Users can edit their own comments for a limited time, while admins can edit all comments.

== Description ==

Users can edit their own comments for a limited time, while admins can edit all comments.

= Features =
<h4>Admin Features</h4>
Please watch this video demonstrating the <a href='http://www.youtube.com/watch?v=b5-ViKb4hfY&fmt=22'>new admin features</a>.
<ul>
<li>Can edit all comments.</li>
<li>Can move comments.</li>
<li>Can blacklist comments.</li>
<li>Can approve, mark for moderation, mark as spam, and delete all comments from a post or the admin panel.</li>
<li>Undo functionality for most editing options.</li>
<li>Can choose different icon sets for use on your blog.</li>
<li>Dropdown menu to de-clutter the interface.</li>
<li>Improved RTL support.</li>
<li>Various admin-panel options including<ul><li>Can edit comment time.</li><li>Can specify anonymous user options.</li><li>Can specify logged-in user options.</li><li>Can choose between Akismet and Defensio for spam protection.</li><li>Can disable timer.</li><li>And much, much more...</li></ul></li>
</ul>
<h4>Anonymous User Features</h4>
Please watch this video demonstrating the <a href='http://www.youtube.com/watch?v=XBveaSEIXW8&fmt=22'>anonymous features</a>.
<ul>
<li>Can edit comments for a limited time.</li>
<li>Can request deletion of own comments.</li>
</ul>
<h4>Logged-in User Features</h4>
<ul>
<li>Potential indefinite comment editing.</li>
<li>Can edit comments for a limited time.</li>
</ul>
<h4>Plugin Features</h4>
<ul>
<li>New pop-up box called Colorbox used.</li>
<li>Timer on both the post and in the comment-editing interface.</li>
<li>Improved usability and look-and-feel.</li>
<li>Better error and status messages.</li>
</ul>

== Changelog ==
= 3.2.1.0 =
* Released 29 May 2011 by Ronalfy
* Updating JavaScript for WordPress 3.2

= 3.2.0.0 = 
* Released 03 March 2011 by Ronalfy
* Fixed colorbox conflict with WordPress 3.1

= 3.1.1 =
* Released 18 February 2009 by Ronalfy
* Fixed minor bug in editing window.
* Ensuring 2.9.2 compatibility.
= 3.1 = 
* Released 19 December 2009 by Ronalfy
* This is the last major free upgrade on WP Extend.  Updates/support should be directed towards <a href="http://www.ajaxeditcomments.com">Ajax Edit Comments.com</a>.
* Added trash support for WP 2.9
* Added affiliate and upgrade support for the AEC forums.
* Added a feature to not load the plugin if the admin has the option of "Only Logged In Users Can Comment" enabled.
= 3.02 = 
* Released 05 December 2009 by Ronalfy
* Fixed bug in themes where comments overlapped.  Adding a BR tag fixed the issue.
* Fixed a bug where timestamps weren't being added to the feed properly.  This issue has been fixed by checking if the content is a feed.  If it is, the plugin won't load.
= 3.01 = 
* Released 03 November 2009 by Ronalfy
* Added in support for previous WordPress versions (+2.5)
= 3.0 = 
* Released 02 November 2009 by Ronalfy
* New pop-up box.  Uses Colorbox instead of Thickbox.  The result should be much faster performance.
* New "undo" function for most editing options.
* New Comment Blacklist feature.
* New Icon Themes feature to select different icon sets.
* New dropdown box to de-clutter the admin editing options.
* Tighter admin integration when editing comment options.
* Numerous style fixes for better theme integration.
* Increased security with better use of nonces.
* Numerous bug fixes.
= 2.4.1.1 = 
* Released 23 October 2009 by Ronalfy
* Fixes a bug in the "Move Comments" feature where IE7/8 users couldn't browse by posts.
= 2.4.1.0 =
* Released 22 October 2009 by Ronalfy
* Fixed "Request Deletion" bug where moderated users couldn't request deletion.
* Fixed "Request Deletion" bug where users could still edit and request deletion after a request has been sent.  The default behavior is that if the user requests deletion, the comment is marked as "unapproved", and the user can no longer see the edit options.
* Fixed an issue when viewing "spam" comments in the WordPress admin panel where several options are missing.  The options on a "spam" comment should now be Edit, Move, De-link, Moderate, Not Spam, and Delete.
* Fixed a bug in the "Move Comment" section where certain posts reflected the wrong comment count after moving.  This issue should now be resolved.
* Fixed a "missing file" error in the admin's editing panel.
* Updated the FAQ section on this website.

= 2.4.0.4 =
* Released 21 October 2009 by Ronalfy
* Fixed bug in IE7/8 where the save window wouldn't close after a successfully saved comment.
* Tested version for compatibility in WordPress 2.8.5.

= 2.4.0.3 = 
* Released 29 September 2009 by Ajay
* Fixed possible security issue. Mandatory upgrade

= 2.4.0.2 = 
* Released 28 September 2009 by Ajay
* Fixed possible security issue. Mandatory upgrade

= 2.4.0.1 = 
* Released 18 September 2009 by Ajay
* Fixed a small bug that spoilt the display of comments


== Upgrade Notice ==

= 3.2.0.0 = 

Resolves a colorbox conflict with WordPress 3.1.

== Screenshots ==

1. Admin comment options on a post.
2. Admin edit comments screen.
3. Admin move comments screen.
4. Admin blacklist comments screen.
5. New dropdown menu options.
6. New styles options.
7. No icons view.
8. Dropdown menu with alternate icons.
9. Anonymous user editing options.

== Installation ==

1. Just unzip and upload the "wp-ajax-edit-comments" folder to your '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

From there, you and your users should be able to edit comments.  This plugin has been tested successfully on Internet Explorer 6-8, Firefox 2,3, and Safari for Mac.

Advanced customization options are available through the admin panel options.  

== Frequently Asked Questions ==

= I installed the plugin and the edit buttons aren't shown or the colorbox popup is off = 

Roughly 90% of the issues Ajax Edit Comments experiences is with multiple versions of jQuery running.  Check your page source, and if you have multiple copies of jQuery being inserted, this is likely the issue.

The most common culprit is the theme in the header.php area.  Please refrain from hard-coding in scripts and use wp_enqueue_script('jquery') before the wp_head() call.

The most common colorbox conflict is when there is another lightbox plugin that uses their own version of colorbox.  

= How do I disable the dropdown menu? = 

Go into Settings->Ajax Edit Comments, and go to the Styles section.  You can adjust the settings there to get rid of the dropdown menu.

= Can I change the icons? =

Go into Settings->Ajax Edit Comments, and go to the Styles section.  From there,  you can choose the icon set. 

= What is the De-link feature? =

If a user posts a comment, but includes a rather spammy link as their URL, you can click the De-link button to remove the person's link.

= What does the "Move Comment" feature do? = 

Sometimes users leave a comment on the wrong post.  With the "Move Comment" feature, you can select the comment, and move the comment to the correct post.  You can do this by searching by the post title, the post ID, or by browsing your recent posts.  Searching by Post Title and Post ID only shows the first five results.  Browsing by posts should allow you access to all of your posts.

= What is the "Request Deletion" feature? = 

The "Request Deletion" feature is for anonymous users who leave comments.  Sometimes a user has left a comment and would rather have it deleted than edit it.  Call it buyer's remorse.

Once the user requests deletion, the comment is automatically "unapproved", the user is stripped of editing rights, and the admin of the blog is sent an e-mail of the deletion request.  The admin can then decide whether to approve, edit, or delete the comment.

= Your plugin doesn't look good with my theme.  How can I fix this? = 

When you go into the Ajax Edit Comments settings page, you'll see a section called "Styles".  From there, you'll be able to enter a "relative" URL to a stylesheet of your choice, which will override the styles provided by Ajax Edit Comments.

It's up to you to provide the relevant styles needed for the plugin in your own stylesheet.

= I upgraded to the latest version of your plugin and it's not working.  What's wrong? = 

Make sure you have the latest WordPress version installed.  If you still have issues, try de-activating your plugins one-by-one to rule out a plugin conflict.  

= What WordPress versions does this plugin work for? = 

We strive for backwards compatibility, but can only support the plugin for the latest stable version of WordPress.

= Can I provide a translation? =

Not for this version.

= Will you provide support for older versions of Ajax Edit Comments (1.x)? =

No.  Ajax Edit Comments 1.x is no longer supported.  We encourage you to upgrade to the latest version of the plugin.

= I have a version of WordPress prior to 2.5.  Is there a plugin for earlier versions? =

Yes, the last stable version for WP versions 2.1 - 2.5 is:  <a href="http://downloads.wordpress.org/plugin/wp-ajax-edit-comments.v1.1.5.1.zip">1.1.5.1</a>.

= Is support free? =

You may utilize the community support features here, however, we're stretched pretty thin.