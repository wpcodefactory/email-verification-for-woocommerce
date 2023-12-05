=== Email Verification for WooCommerce ===
Contributors: wpcodefactory, omardabbas, karzin, anbinder, algoritmika, kousikmukherjeeli
Tags: woocommerce, email, verification, email verification, woo commerce
Requires at least: 4.4
Tested up to: 6.4
Stable tag: 2.6.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Verify user emails in WooCommerce. Beautifully.

== Description ==

**Email Verification for WooCommerce** plugin lets you add email verification to WooCommerce.

### &#9989; Main Features ###

* Require **email verification** for new user registrations.


* Optionally enable email verification for **already registered users**.


* Skip email verification for selected **user roles**.


* Customize **verification messages** on frontend.


* Optionally manually **verify**, **unverify**, **resend** email activation link **by admin**.


* Optionally **delete unverified users** from the database (manually or automatically once per week, day or hour).


* Select if you want to send verification as a **separate email**, or **append** it to the standard WooCommerce "Customer new account" email.


* **Delay** standard WooCommerce **"Customer new account" email** until after successful verification (in a separate email).


* **Prevent** automatic user **login after registration**, including registration during checkout.


* **Content blocking**: Block content from unverified users.


* And more...

### &#127942; Premium Version ###

[Email Verification for WooCommerce Pro](https://wpfactory.com/item/email-verification-for-woocommerce/) features:

* **Activation email customization options**, including wrapping in standard WooCommerce email template.


* **Block "Thank you"** (i.e. "Order received") **page** access for non-verified users.


* **Block** standard WooCommerce customer **order emails** ("Order on-hold", "Processing order", "Completed order") for all non-verified users.


* **Block guests** from adding products to the cart.


* **Block checkout process** for non-verified users.


* **Content blocking customization**: Block content from unverified users like products or block content by function, as shop pages, category pages and more. Customize the error notice and the page the unverified user is going to be redirected to in case he tries to access the content.


* Set activation link **expiration time**.


* Send **email to the admin** when a new user verifies his email.


* Set emails on a **denylist**.


* Automatically unverify users who have **changed their emails**.


* Automatically verify users on **password reset**.


* **Customize** the **verification info** completely.


* **REST API**: Verify users using the REST API with the `alg_wc_ev/v1/verify` endpoint.


* **Compatibility** options with:
  * [Social Login - WPWeb](https://woocommerce.com/products/woocommerce-social-login/) plugin.
  * [Social Login - SkyVerge](https://codecanyon.net/item/woocommerce-social-login-wordpress-plugin/8495883) plugin.
  * [Super Socializer](https://wordpress.org/plugins/super-socializer/) plugin.
  * [Nextend Social Login](https://codecanyon.net/item/woocommerce-social-login-wordpress-plugin/8495883) plugin.
  * [WooMail](https://codecanyon.net/item/email-customizer-for-woocommerce-with-drag-drop-builder-woo-email-editor/22400984) plugin.

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/email-verification-for-woocommerce/).

== Frequently Asked Questions ==
= User registration clears shopping cart? =
If your cart is getting empty after a new account is created, there are some things you could try:
 - Enable the option **Advanced > Custom logout function**.
 - Set the **Advanced > Prevent login after register > Login prevention method** option as **Use login filter from WooCommerce**.

= When a user registers, it gives an error. How can I fix it? =
Set the **Advanced > Prevent login after register > Login prevention method** option as **Use login filter from WooCommerce**.

= How to eliminate spam registrations? =
If you use some options combined, you'll be able to remove the unverified users automatically, eliminating spam registrations.
This is what you can do:

- Disable **General > Enable email verification for already registered users** (This is optional, however it's more safe)
- Enable **Advanced > Delete users automatically**
- Set **General > Expire time** (This is optional but it's more safe, because you'll be removing only unverified users whose activation have expired. This is a [Pro](https://wpfactory.com/item/email-verification-for-woocommerce/) feature though)

= What can I do if I get a 403 error or have issues when trying to save settings? =
Some servers have security solutions that do not allow saving settings with HTML tags.
You can try to contact them asking to disable it, or you can try our option:

- **Advanced > Replace HTML tags**

It will try to convert the angle brackets from HTML tags by other characters.
After that, you'll just need to save the settings page once more. On the frontend the characters will be converted to HTML tags again.

= Why the activation message ("Thank you for your registration...") is not getting displayed after registration? =
Please try to make sure that at least one of the above options are enabled.
If just some of them are already enabled and even so it doesn't work, try to enable all of the them:

-  **Advanced > Prevent login after register**
-  **Advanced > Prevent login after checkout**
-  **Logout unverified users on "My Account" page**
-  **Logout unverified users on every page**

= How to prevent duplicated success message after account verification? =
Please try to use **General > Redirect on success** option as **Do not redirect**

= If a unverified user tries to login how to priorize verification error message over incorrect password ? =
Try to set the **Advanced > Authenticate filter** option as **authenticate filter**

= What can I do if the error messages are not showing? =
If an unverified user is trying to login and the error message (Your account has to be activated before you can login...) is not getting displayed you can try two different approaches:

1. Use the **Redirect on failure** option.
Even if the **Custom redirect URL** option is empty, there will be no problem.
A `?alg_wc_ev_email_verified_error` argument will be added to the URL that could help you displaying the message.

2. Change the **Advanced > Authenticate filter** option.

= How to use the alg_wc_ev_verification_status shortcode ? =
You can use the `[alg_wc_ev_verification_status]` shortcode to display the verification status, showing if current user is verified or not.
Params for the `[alg_wc_ev_verification_status]` shortcode:

- **wrapper_template**: `<div class="alg-wc-ev-verification-status">{content_template}</div>`
- **content_template**: `Verification status: {verification_status}`
- **verified_status**: `Verified`
- **unverified_status**: `Unverified`
- **hide_if_verified**: `false`
- **hide_for_guests**: `false`

**wrapper_template** params:

- **{content_template}**

**content_template** params:

- **{verification_status}**
- **{user_display_name}**
- **{user_nicename}**

= How to use the alg_wc_ev_resend_verification_url shortcode ? =
You can use the `[alg_wc_ev_resend_verification_url]` shortcode to display a message with a link to the verification url.
Params for the `[alg_wc_ev_resend_verification_url]` shortcode:

- **wrapper_template**: `<div class="alg-wc-ev-resend-verification-url">{content_template}</div>`
- **content_template**: `You can resend the email with verification link by clicking <a href="{resend_verification_url}">here</a>.`
- **hide_if_verified**: `true`
- **hide_for_guests**: `false`

**content_template** params:

- **{resend_verification_url}**

= How to use the alg_wc_ev_email_content_placeholder shortcode ? =
You can use the `[alg_wc_ev_email_content_placeholder]` shortcode to append the verification email to some custom email template.
In order to use it, it's necessary to:

- Enable the option **Email > Activation email > Fine tune activation email placement**
- Disable the option **Email > Activation email > Send as a separate email**
- Most probably the option **Emails > Activation email > Email template** should be set as **Plain**

Params for the `[alg_wc_ev_email_content_placeholder]` shortcode:

- **user_email**

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Email Verification".

== Changelog ==

= 2.6.3 - 05/12/2023 =
* Fix - When the option "WC email template" is set to "WooCommerce > Emails", the activation and confirmation emails can't have their content changed via settings.
* WC tested up to: 8.3.
* Tested up to: 6.4.

= 2.6.2 - 22/10/2023 =
* WC tested up to: 8.2.
* Fix - Deprecated warning!

= 2.6.1 - 25/09/2023 =
* Tested up to: 6.3.
* Update plugin icon, banner.

= 2.6.0 - 14/09/2023 =
* Fix - Class `WC_Email` not found in some circumstances.
* Dev - General - Account verification - New option: "Verification parameter".
* WC tested up to: 8.1.

= 2.5.9 - 07/08/2023 =
* Fix - restrict default validate at checkout page.

= 2.5.8 - 06/08/2023 =
* Dev - New option: General > Account Verification > Guest users.
* WC tested up to: 7.9.

= 2.5.7 - 03/07/2023 =
* Dev - Declare compatibility with HPOS.

= 2.5.6 - 17/06/2023 =
* WC tested up to: 7.8.

= 2.5.5 - 17/04/2023 =
* Fix - Undefined array key "hide_for_guests" on `Alg_WC_Email_Verification_Core`.

= 2.5.4 - 17/04/2023 =
* Dev - Improve the `send_auth_cookies` filter.

= 2.5.3 - 15/04/2023 =
* Dev - Compatibility - Woodmart - Auto verify users from WoodMart social authentication.
* Dev - Advanced - Authenticate filter - New option: `send_auth_cookies`.
* WC tested up to: 7.6.
* Tested up to: 6.2.

= 2.5.2 - 21/03/2023 =
* Fix - Compatibility - VillaTheme Email Customizer - Activation/Confirmation email don't get available as Email types.
* WC tested up to: 7.5.

= 2.5.1 - 23/02/2023 =
* Dev - Compatibility - YayMail - New option: Append the Activation email message to the "Customer new account" email using the `[yaymail_custom_shortcode_alg_wc_ev_aem]` shortcode.
* WC tested up to: 7.4.

= 2.5.0 - 04/01/2023 =
* Fix - Email - Email options - Delay Customer new account email forces the email to be always active.
* Dev - Improve the way of initializing the main class.
* WC tested up to: 7.2.

= 2.4.9 - 15/12/2022 =
* Fix - Possible error regarding composer dependencies.

= 2.4.8 - 30/11/2022 =
* Dev - Email - Confirmation email - New option: Send confirmation email to the user manually verified by admin.
* Dev - Email - Admin email - New option: Send admin email when a user has been manually verified by admin.

= 2.4.7 - 24/11/2022 =
* Dev - Blocking - Block content - New option: Block by URL(s).

= 2.4.6 - 21/11/2022 =
* Fix - Advanced - Prevent login after register - Use login filter from WooCommerce blocks checkout.
* WC tested up to: 7.1.
* Tested up to: 6.1.

= 2.4.5 - 19/10/2022 =
* Dev - Improve code to filter users on admin.
* WC tested up to: 7.0

= 2.4.4 - 06/10/2022 =
* Fix - Email domain on translation function is not correct.
* Fix - Add untranslated strings to POT.

= 2.4.3 - 28/09/2022 =
* Dev - Messages - New option: "Already verified" message.
* Dev - Compatibility - Polylang - Add option to translate WooCommerce pages with the purpose of sending the activation link and the emails with the correct language.
* Fix - Email - Customer new account email - Delay Customer new account email is being sent along with the activation email.

= 2.4.2 - 16/09/2022 =
* Fix - General - Account verification - "Verify account for current users" is not sending emails.
* WC tested up to: 6.9.

= 2.4.1 - 29/08/2022 =
* Fix `.gitattributes` regarding `git-tag.sh`.
* Dev - Emails - Confirmation email - New option: Delay - Wait for some time before sending the email.
* Dev - Emails - Confirmation email - New option: Delay - Unit of time.
* Dev - Emails - Confirmation email - New option: Delay - Delay value.

= 2.4.0 - 26/08/2022 =
* Dev - Advanced - Encoding options - New option: Encoding method.
* Dev - Advanced - Encoding options - New option: Hashids - "Hashids salt".
* Dev - Advanced - Encoding options - New option: Hashids - "Hashids alphabet".

= 2.3.9 - 16/08/2022 =
* Dev - Compatibility - Email Customizer for WooCommerce by VillaTheme - Add option to enable placeholders on the email templates.
* WC tested up to: 6.8.

= 2.3.8 - 06/07/2022 =
* Dev - Admin - Added bulk option to unverify users and integrate support for background processing.

= 2.3.7 - 20/06/2022 =
* Fix - Shortcode - Add success and failure messages depending on the user status upon submission of verification form.
* Fix - General - Redirect on success - Store the referer URL on a better place to use later for redirection.
* WC tested up to: 6.6.

= 2.3.6 - 07/06/2022 =
* Fix - Error message being displayed on verification.
* Dev - New parameter `submit_btn_template` added to the shortcode `alg_wc_ev_resend_verification_form` to control submit button of the form.
* Dev - Emails - Create new "WC email template" option that adds new emails (activation, confirmation) to "WooCommerce > emails".
* Dev - Emails - Create new "WC email template" option that adds new emails (activation, confirmation) to "WooCommerce > emails".

= 2.3.5 - 30/05/2022 =
* Fix - Creating a new account triggers sometimes the error "Call to undefined function `wc_has_notice()`".
* Fix - Blocking - Block account verification by email - Blocks some users when it should not.
* Dev - Shortcode - Add `[alg_wc_ev_new_user_info]` shortcode to display user information for users who have just registered.
* Dev - Shortcode - Add `[alg_wc_ev_resend_verification_form]` shortcode to display resend verification form.
* Dev - General - Account verification - Create new option to manage the message displayed to the user who has just changed his email.
* Dev - General - Redirect on success - Create new option to redirect to the previous page the user was before accessing my account page.
* Dev - Compatibility - Add `{alg_wc_ev_viwec}` special text for Email Customizer plugin by VillaTheme.
* WC tested up to: 6.5.
* Tested up to: 6.0.

= 2.3.4 - 02/05/2022 =
* Fix - Success message does not get displayed after verification when "Redirect on success" is disabled.
* Dev - Advanced - Add "Session start params" option.
* Dev - Advanced - Prevent login after register - Create "Login prevention method" option.
* Dev - Add `alg_wc_ev_session_start_params` filter.

= 2.3.3 - 26/04/2022 =
* Fix - Admin - "Allowed user roles" option needs to work empty as well, allowing all user roles to access the plugin settings.
* Dev - Admin - Added bulk verification of users and integrate support for background processing.
* Dev - Admin - Added status filter option in Users list page.
* WC tested up to: 6.4.

= 2.3.2 - 04/04/2022 =
* Dev - Compatibility - Paid Memberships Pro - Add option to verify users that signs up via Paid Memberships Pro registration process.
* Dev - Compatibility - Paid Memberships Pro - Add option to verify users that already have a valid membership.

= 2.3.1 - 21/03/2022 =
* Fix - Emails - Confirmation email - Subject does not reflect the settings.
* Fix - Emails - Confirmation email - Wrong email heading.
* Dev - Emails - Create `alg_wc_ev_email_subject_final` filter.
* Dev - Emails - Create `alg_wc_ev_email_content_heading` filter.
* Dev - Emails - Move "Email template" and "Email wrap method" options from activation email to general email section.
* Dev - Emails - Code refactoring.

= 2.3.0 - 21/03/2022 =
* Fix - Emails - Confirmation email - Call to undefined method `Alg_WC_Email_Verification_Emails::wrap_in_wc_email_template()`.

= 2.2.9 - 18/03/2022 =
* Dev - Move compatibility code to a new class.
* Dev - Emails - Implement functionality of sending confirmation email to user.
* WC tested up to: 6.3.

= 2.2.8 - 02/03/2022 =
* Dev - Improve `Alg_WC_Email_Verification_Logouts::block_unverified_user_login()`.
* Dev - Compatibility - Essential Addons for Elementor - Add option to Verify users who register or log in from Login Register form element.
* WC tested up to: 6.2.

= 2.2.7 - 31/01/2022 =
* Dev - Compatibility - Email Customizer - Create option that allows a `alg_wc_ev_ec_email_content` action hook display the activation email content.
* Dev - Add more strings to `wpml-config.xml`.
* Tested up to: 5.9.

= 2.2.6 - 19/01/2022 =
* Fix - Blocking - Block order emails - Users don't receive the emails when accounts are activated automatically after the order is paid.
* Dev - Move "Advanced > Block order emails" to "Blocking" section.
* Dev - Remove `$code` param from `alg_wc_ev_user_account_activated` and `alg_wc_ev_verify_email_error` actions.
* Dev - Change `Alg_WC_Email_Verification_Core::verify()` parameter from `is_rest_api` to `directly`.
* WC tested up to: 6.1.

= 2.2.5 - 10/12/2021 =
* Fix - Users can't activate the account.

= 2.2.4 - 08/12/2021 =
* Dev - Move "Auto verify paying customers" to General > Account verification.
* WC tested up to: 5.9.

= 2.2.3 - 21/10/2021 =
* Dev - General - Verification info - Add customization content to `wpml-config.xml`.

= 2.2.2 - 15/10/2021 =
* Fix - Clicking on resend link from WP 2FA plugin triggers an error.
* WC tested up to: 5.8.

= 2.2.1 - 12/10/2021 =
* Fix - Check for `WP_Background_Process` class before trying to use it.

= 2.2.0 - 07/10/2021 =
* Dev - Admin - Add "Allowed user roles" option allowing to manage which user roles will interact with the admin interface from the plugin.

= 2.1.9 - 27/09/2021 =
* Dev - Email - Create `wpml-config.xml` file with admin email options.
* Improve admin settings.
* WC tested up to: 5.7.

= 2.1.8 - 15/09/2021 =
* Fix - User can't resend activation email if "Send as a separate email" option is disabled.
* Fix - Email - Activation email - Change "Email content" default value in order to prevent possible issues from some email services like Outlook/Hotmail.
* Dev - Email - Activation email - Create "Smart" value to "Email template" option.

= 2.1.7 - 13/09/2021 =
* Fix - Email - Verify nonce in order to resend the activation email.

= 2.1.6 - 27/08/2021 =
* Fix - General - Redirect on success option.
* Dev - Functions - Create the param `check_previous_messages` to check if the message has been added previously.
* Dev - Improve `is_plugin_active()` function.
* WC tested up to: 5.6.

= 2.1.5 - 19/08/2021 =
* Dev - Advanced - Improve "Authenticate filter" option.
* Improve readme.

= 2.1.4 - 09/08/2021 =
* Fix - Possible duplicated activation message.
* Dev - General - Add new option to verify the account if password is reset.
* Dev - Advanced - Add option to use `alg_wc_ev/v1/verify` REST API endpoint.
* Dev - Improve main `verify()` function.
* Dev - Improve one-time activation link function.
* Reorganize general section on admin settings.

= 2.1.3 - 28/07/2021 =
* Dev - Add `[alg_wc_ev_email_content_placeholder]` shortcode with `user_email` param.
* Create the compatibility section.
* Tested up to: 5.8.

= 2.1.2 - 17/07/2021 =
* Fix shortcode documentation format on FAQ.

= 2.1.1 - 17/07/2021 =
* Dev - Blocking - Create "Blocked pages" option.
* Dev - Blocking - Create "Blocked products" option.
* Dev - Blocking - Create "Conditionals" option allowing to block content by checking the conditionals.
* Dev - Blocking - Create "Redirect" option.
* Dev - Blocking - Create "Error" options.
* Dev - General - Verification info - Create "My account page" option.
* Dev - General - Verification info - Create "Widget" option.
* Dev - General - Verification info - Create "Customization" option.
* Dev - Add `[alg_wc_ev_verification_status]` shortcode with `wrapper_template`, `content_template`, `hide_for_guests`, `hide_if_verified`, `verified_status` and `unverified_status` params.
* Dev - Add `[alg_wc_ev_resend_verification_url]` shortcode with `wrapper_template`, `content_template` `hide_for_guests`, params.
* Dev - Improve `is_user_verified()` function.
* Dev - Improve verification url encoding/decoding by sanitizing characters like `+/=`.
* Dev - Rearrange admin settings.
* Dev - Add github deploy setup.
* WC tested up to: 5.5.

= 2.1.0 - 18/06/2021 =
* Fix - Free and pro plugins can't be active at the same time.
* Dev - Use wpf-promoting-notice library to add notice on settings page regarding pro version.
* Dev - General - Add "Redirect on failure" option.
* Add FAQ question regarding error messages not getting displayed.
* Add composer setup.
* WC tested up to: 5.4.

= 2.0.9 - 20/05/2021 =
* Dev - Messages - Create "Clear previous messages" option trying to avoid duplicated messages.
* Dev - Advanced - Prevent login after register - Create "Force redirect" option.
* WC tested up to: 5.3.

= 2.0.8 - 09/04/2021 =
* Fix - Account is not verified if "Send verification as a separate email" is disabled and "Delay Customer new account email" is enabled.
* Dev - Advanced - Block order emails - Add "Blocked emails" option.
* Dev - Advanced - Block order emails - Add "Unblock emails" option.
* Dev - Advanced - Add compatibility option with "WooMail - WooCommerce Email Customizer" plugin.
* Dev - General - Add "Block unverified login" option.
* Add notice on settings page regarding pro version.
* Advanced - Rearrange admin settings.
* WC tested up to: 5.1.

= 2.0.7 - 22/03/2021 =
* Fix - Advanced - Fix compatibility with "WooCommerce Social Login (SkyVerge)" plugin.
* Dev - Advanced - Add "Block auth cookies" option.
* Dev - Messages - Resend verification URL - Add "URL" option.
* Move "Mail function" and "Fine tune activation email" options to "Emails" section.
* Tested up to: 5.7

= 2.0.6 - 25/02/2021 =
* Fix - General - Activation link - Increase "Activation email delay" priority to fix possible conflicts with 3rd party plugins like "Kadence WooCommerce Email Designer".
* Fix - Checks `$data['id']` from activation time.
* Dev - Emails - Activation email - Add "Email wrap method" option.
* WC tested up to: 5.0

= 2.0.5 - 15/01/2021 =
* Fix - Conflict between WCMP plugin and "Delay WooCommerce Customer new account email" option making vendor template emails disappear from WooCommerce settings.
* Fix - Increase priority on `redirect_on_success_activation()` from `alg_wc_ev_user_account_activated` hook.

= 2.0.4 - 13/01/2021 =
* Fix - General - Activation link - Activation email delay.
* Dev - Advanced - Add "Fine tune activation email placement" option.
* Dev - Advanced - Fine tune activation email placement - Add callback for the new `alg_wc_ev_activation_email_content_placeholder` hook allowing to fine tune the activation email placement inside the "Customer new account" email.
* WC tested up to: 4.9

= 2.0.3 - 08/01/2021 =
* Fix - General - "One-time activation link" option.
* Dev - General - Block adding products to cart - Add "Custom redirect URL" option.
* Dev - Add "Verify paying customers automatically" option.
* Dev - Add "Unverify email changing" option.
* Add missing activation message question to FAQ.

= 2.0.2 - 18/12/2020 =
* Fix - Check for possible previous error before checking if user is verified to show the error message.
* Fix - Advanced - Fix compatibility with "Super Socializer" plugin.
* Dev - Advanced - Add compatibility with "Social Login" from My Listing theme.
* Dev - Advanced - Create "Authenticate filter" option.
* Dev - General - Activation Link - Add a "Activation email delay" option, which tries to prevent the activation email from being sent to already authenticated users.
* Move compatibility options to Advanced section.
* WC tested up to: 4.8
* Tested up to: 5.6

= 2.0.1 - 10/12/2020 =
* Fix - Admin Options - Delete users - Delete users in background processing avoiding possible server errors.
* Fix - Admin Options - Info about the scheduled event from "Delete users automatically" option.
* Dev - General Options - Add compatibility option with "WooCommerce Social Login" plugin made by WooCommerce author SkyVerge.
* Dev - Advanced Options - Background Processing - Add "Minimum amount" option.
* Dev - Advanced Options - Background Processing - Add "Send email" option.
* Dev - Advanced Options - Background Processing - Add "Email to" option.

= 2.0.0 - 03/12/2020 =
* Fix - Success message not getting displayed after redirect.
* Dev - General - Add "Login automatically" option.
* Dev - Advanced Options - Add "Replace HTML tags" option.
* Dev - Advanced Options - Add "Delete users frequency" option.
* Dev - Admin Options - Add info about the scheduled event from "Delete users automatically" option.
* Dev - Filters - `alg_wc_ev_html_replacement_params` filter added.
* Add "Replace HTML tags" option to readme FAQ.
* Add "How to eliminate Spam registrations?" on readme.
* Move "Prevent automatic user login" section from general to advanced.

= 1.9.8 - 24/11/2020 =
* Fix - Settings - Use `wp_kses_post` instead of leaving the `$raw_value`.
* Dev - General Options - Add "Expire time unit" option.
* WC tested up to: 4.7

= 1.9.7 - 08/11/2020 =
* Fix `load_plugin_textdomain` call by putting it inside the 'init' hook.
* Dev - Emails - Admin email - Allow template variables to be used on 'Subject' and 'Heading'.
* Dev - General Options - Add compatibility with "Nextend Social Login" plugin.
* Dev - General Options - Add compatibility with "Super Socializer" plugin.
* Dev - General Options - Delay option will now send the automatically generated password on email by regenerating it again.
* Add social login link on readme.

= 1.9.6 - 07/10/2020 =
* Fix - Wrong text-domain on advanced settings.
* Fix - 'One-time activation link' option triggering improper error message after successful login.
* Dev - General Options - Advanced - Improve 'Custom "logout" function' option description.
* Dev - Admin - Add 'Resend verification email' option for bulk users actions.
* Add 'User registration clears shopping cart' FAQ question.

= 1.9.5 - 08/09/2020 =
* Dev - General - Add 'Block non-paying users' option.
* Dev - General - Block non-paying users - Add 'Role checking' option.
* Dev - General - Block non-paying users - Add 'Send activation email only on payment' option.
* Dev - General - Block non-paying users - Add 'Error notice' option.
* Dev - General - Add 'One-time activation link' option making the activation link usable only once.
* Dev - Admin - Add 'Verified column position' option allowing to setup the column position.
* Dev - Filters - `alg_wc_ev_reset_and_mail_activation_link_validation` filter added.
* Dev - Filters - `alg_wc_ev_block_unverified_user_login_error_message` filter added.
* Dev - Functions - `alg_wc_ev_is_valid_paying_user()` function added.
* Improve verified icons on admin using dashicons.
* Improve verified column actions.
* Rearrange admin settings.
* WC tested up to: 4.4

= 1.9.4 - 14/08/2020 =
* Plugin author updated.
* Tested up to: 5.5.

= 1.9.3 - 08/08/2020 =
* Dev - Code refactoring.

= 1.9.2 - 05/08/2020 =
* Dev - Advanced - "Mail function" option added.
* Dev - Code refactoring.

= 1.9.1 - 03/08/2020 =
* Dev - General - Redirect on success - "Redirect to custom URL" option added.

= 1.9.0 - 24/07/2020 =
* Dev - General - Prevent automatic user login after register - "Redirect" option moved from the "Advanced" section; "Custom redirect" option added.
* Dev - Emails - Email content - New placeholders added: `%user_id%`, `%user_first_name%`, `%user_last_name%`, `%user_login%`, `%user_nicename%`, `%user_email%`, `%user_display_name%`.
* Dev - Emails - WooCommerce template - Footer - Fallback `replace_placeholders()` function added (to ensure that e.g. `{site_title}` placeholder is replaced).
* Dev - Filters - `alg_wc_ev_redirect_on_registration` filter added; `alg_wc_ev_after_redirect_checkout` filter renamed to `alg_wc_ev_redirect_after_checkout`.
* Dev - Functions - `alg_wc_ev_is_user_verified_by_user_id()` and `alg_wc_ev_is_user_verified()` functions added.
* Dev - Code refactoring.
* WC tested up to: 4.3.

= 1.8.3 - 08/06/2020 =
* Dev - General Options - 'Logout unverified users on "My Account" page' option added.
* Dev - General Options - 'Redirect to "My account"' option renamed to "Redirect on success", and 'Redirect to "Shop" page' and "Redirect to home page" options added.
* Dev - Advanced Options - 'Force redirect on "Prevent automatic user login after register"' option added.
* WC tested up to: 4.2.

= 1.8.2 - 26/05/2020 =
* Fix - Security vulnerability fixed.

= 1.8.1 - 23/05/2020 =
* Fix - General Options - Send as a separate email - Correctly marking user as unverified now (when both "Send as a separate email" and "Enable email verification for already registered users" options are disabled).
* Dev - General Options - Logout unverified users on every page - "Redirect" option added (defaults to `yes`).
* Dev - General Options - Prevent automatic user login after checkout - Admin settings description updated.

= 1.8.0 - 22/05/2020 =
* Dev - General Options - "Send as a separate email" option added.
* Dev - General Options - "Logout unverified users on every page" options added.
* Dev - General Options - "Block checkout process for unverified users" options added.
* Dev - General Options - Blacklist emails - Now accepting multiple lines in settings.
* Dev - Admin Options - `manage_users_custom_column` hook priority increased.
* Dev - Admin Options - Settings descriptions updated.
* Dev - Saving "email sent" data in user meta ("activation", "WooCommerce customer new account", "admin" emails).
* Dev - Double checking if email was already sent ("WooCommerce customer new account", "admin" emails).
* Dev - `is_user_verified()` - Double checking guests.
* Dev - Code refactoring.
* Localization - `fr_FR` translation added.
* Tags updated.

= 1.7.0 - 08/05/2020 =
* Dev - General Options - "Expire activation link" options added.
* Dev - General Options - "Enable plugin" option removed.
* Dev - Admin Options - "Delete users automatically" option added.
* Dev - Admin Options - Delete users - `alg_wc_ev_delete_unverified_users_loop_args` filter added.
* Dev - Advanced Options - 'Notice for "Prevent automatic user login after checkout"' moved from "General" settings section.
* Dev - `[alg_wc_ev_translate]` shortcode added.
* WC tested up to: 4.1.

= 1.6.0 - 01/05/2020 =
* Dev - General Options - "Blacklist emails" options added.
* Dev - General Options - 'Accept verification from "WooCommerce - Social Login" plugin' option added.
* Dev - Admin Options - Users list column - Now checking "Skip email verification for user roles" and "Enable email verification for already registered users" option values when marking users as "verified".
* Dev - Admin Options - Users list column - Actions - "Resend" and "Unverify" admin actions added.
* Dev - Admin Options - Users list column - Actions - Notices added.
* Dev - Advanced Options - "Action for sending activation link email" option added.
* Dev - "Advanced" settings section added.
* Dev - Forcing to send activation email for non-verified users only now.
* Dev - `alg_wc_ev_verify_email` filter added.
* Dev - `alg_wc_ev_is_user_verified` filter added.
* Dev - `alg_wc_ev_new_user_action` filter added.
* Dev - Code refactoring.

= 1.5.1 - 27/04/2020 =
* Fix - Messages - Grammar errors fixed in the default messages (Resend: "... has been resend..." to "... has been resent..."; Failed/Error: "... can resend email..." to "... can resend the email...").
* Dev - General Options - Prevent automatic user login after register - Ensuring that `woocommerce_registration_auth_new_customer` is always `true`.
* Dev - General Options - Advanced - Action for "Prevent automatic user login after checkout" - 'On "'thank you' page"' option added (fixes the issue with CartFlows plugin).
* Dev - General Options - Restyled.

= 1.5.0 - 22/04/2020 =
* Fix - General Options - Prevent automatic user login after checkout - Logging out only unverified users now.
* Fix - General Options - Prevent automatic user login after checkout - Displaying "Activate" notice only for non-guests now (i.e. when guest checkout is allowed).
* Dev - General Options - Prevent automatic user login after checkout - 'Block "Thank you" page' option added.
* Dev - General Options - Prevent automatic user login after checkout - "Block customer order emails" option added.
* Dev - General Options - "Block guests from adding products to the cart" options added.
* Dev - General Options - Advanced - 'Action for "Prevent automatic user login after checkout"' option added.
* Dev - Admin Options - "Email" options added.
* Dev - `alg_wc_ev_core_loaded` action added.
* Dev - Settings - Restyled and descriptions updated.
* Dev - Code refactoring.

= 1.4.2 - 19/04/2020 =
* Fix - Emails - Email template - WooCommerce - Placeholders (e.g. `{site_title}`) are now replaced in footer text.

= 1.4.1 - 17/04/2020 =
* Dev - General Options - Advanced - 'Custom "logout" function' option added.
* Dev - Admin action link description updated.

= 1.4.0 - 17/04/2020 =
* Fix - General Options - Prevent automatic user login after checkout - Zero sum order bug fixed.
* Dev - General Options - Prevent automatic user login after checkout - "Add notice" option added.
* Dev - `%resend_verification_url%` - Using current URL (instead of "My account" URL) now.

= 1.3.1 - 08/04/2020 =
* Dev - General Options - "Prevent automatic user login after register" option added (defaults to `yes`).
* Dev - General Options - "Prevent automatic user login after checkout" option added (defaults to `yes`).
* Dev - Messages - Activate - `%resend_verification_url%` placeholder added.

= 1.3.0 - 03/04/2020 =
* Fix - "Reset settings" admin notice fixed.
* Dev - General Options - Skip email verification for user roles - "Guest" role removed.
* Dev - Admin Options - "Delete users" tool (deletes unverified users) added.
* Dev - Settings split into sections.
* Dev - `alg_wc_email_verification_after_save_settings` action added.
* Tested up to: 5.4.

= 1.2.0 - 13/03/2020 =
* Dev - General Options - 'Standard WooCommerce "Customer new account" email' (delay) option added.
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* Tested up to: 5.3.
* WC tested up to: 4.0.

= 1.1.1 - 10/11/2019 =
* Fix - Text domain changed to `emails-verification-for-woocommerce`.

= 1.1.0 - 10/11/2019 =
* Fix - Automatic logging in on "Create account" from checkout disabled.
* Dev - Admin Options - "Manual verification" option added.
* Dev - Admin Options - "Add column" option added.
* Dev - Shortcodes are now processed in "Email template > WooCommerce heading".
* Dev - Code refactoring.
* Plugin URI updated.
* WC tested up to: 3.8.
* Tested up to: 5.2.

= 1.0.0 - 27/05/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.