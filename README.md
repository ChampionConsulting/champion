
# Champion 6

The simplest flat-file CMS just got a hell of a lot better.

# Key Requirements & Specifications:

Server Environment: Apache web server is required.
PHP Version: PHP 7.4 or higher is required.
Database: None (Flat-file system).
Installation: Involves uploading files to a web host, requiring a php enabled environment.
Memory/Storage: Very low, designed to be lightweight.

License: Free for development; paid license required for commercial/live sites. 

For optimal performance, ensure your host supports PHP 8+ and URL rewriting (for clean URLs).

# DISTRIBUTION WORKFLOW:

* The Cache (x3) directories in championcore/storage needs to be set to 777 writable permissions (championcore/storage/cache, championcore/storage/log and championcore/storage)
* Delete championcore/storage/config.json (the config.json can be removed for the distribution. The installer does create it from scratch)
* Delete backups
* Delete sitemap
* ZIP REPO minus readme.md
* Upload to P6 distribution
* Update version history
* Upload P6 ZIP to free trial server
* Upload latest manual PDF



# Tag notes for now
* See official docs at: [https://cms.championconsulting.com/docs/]
{media_player}
{navigation}
{thumbs:"gallery"}
{masonry:gallery1}
{{blog-show:"meep":"blog":"[[featured-image]] [[blog-content-loop(<> <> <> <> <>)]]"}}
{{blog-show:"":"[[featured-image]] [[blog-content-loop(<<blog-item-author>> <<blog-item-date>> <<blog-item-featured-image>> <<blog-item-content>>)]]"}}
{blog-list:"blog"}
{breadcrumb}
{blog-tags}
{justforms}
{sb_login:test:1234567890:testblock}
{custom_post_type:book/hercules}
{policy} // {{policy:company:location}}
  
# Testing Guide
Use these test whenever you are writing code, after you made a change.

# Quick Test aka "Sanity Check" - 180 seconds to complete

## General
- Go to admin/install.php to set it up correctly.
- Open site in **incognito** window so you see what it's link without being logged in
- Click at least 2 other pages (ex: about page, portfolio page)
- At some point, quickly test as both a subpage (ex: https://mydomain.com/my_subpage/ or https://localhost/my_subpage) and also a root page (ex: http://mydomain.com or https://localhost). It is common that links or pages break due to this path change! So please check carefully.
- Try sub-navigation in navigation menu

## Blog
- Click the blog page. Ensure it loads. 
- Click "next" on the blog to test pagination. 
- Click into 1 blog.

## Mobile
- Mobile test: Only if changing front-end code
- Test mobile home page using Developer Tools.
- Click the mobile menu "hamburger" in the corner. Ensure it loads.
- Go to 1 other page (ex: About page)
- Go to blog and ensure it loads properly in mobile

## Admin panel
- Test logging in as admin. Works? Do pages look broken when logged in?
- Save any setting you'd like as a test

## Test site editor
- Log in, and edit the site text a bit to ensure it saves


# Full Test - 15 minutes to complete
## General
- Go to admin/install.php to set it up correctly.
- Open site in incognito window
- Click at least 2 other pages (ex: about page, portfolio page)
- Try sub-navigation in navigation menu
- **Do entire Full test as both a subpage (ex: mydomain.com/my_champion_page) and a root page (ex: mydomain.com).** It is __very__ common that people forget to adjust paths for root pages or subpages.

## Blog
- Click the blog page. Ensure it loads. 
- Click "next" on the blog to test pagination. 
- Click into 1 blog.
- Test blog search
- Test a Gallery page on mobile
- Test a Slider page on mobile
## Mobile
- Go back to home page. 
- Mobile test:
- Test mobile using Developer Tools.
- Click the mobile menu "hamburger" in the corner. Ensure it loads.
- Go to 1 other page (ex: About page)
- Go to blog and ensure it loads properly in mobile

## Galleries and visual
- Go to a page with a **gallery**, ensure Gallery loads and UX looks/works properly.
- Go to a page with a **slider**, ensure Slider loads and UX looks/works properly.
- Try both points above on Mobile to ensure they still work on Mobile.

## Logged in Mode
Test all items above aside from Mobile when logged in as admin.

## Admin panel
- Test logging in as admin - works? Does page look OK or broken when logged in?
- Test clicking each tab to ensure each one renders
- Test saving a block
- Test saving a page

## Test an update
- Get another version of PulseCMS or make a new one and use CLI to build it.
- Test updating the CMS.

## Test forms
- Test the default contact form.
- Test a JustForms form.

# Templates: Checklist when making a template
Dec 6, 2020

## Navigation
- Check navigation. Remember options for easily inserting CSS classes, ex:
	{{navigation:"all":"collapse navbar-collapse":"navbar-nav ml-auto":"nav-item":"nav-link js-scroll-trigger"}}
- Check subnavigation. Does it work?
- Check internal links to other pages. Does clicking the link work?
- Highlight of "active" navigation. Test on multiple pages. 
	Remember it revolves around the CSS class called "active".

## Logged-In Operations:
	This is about whether the template/page works when you are logged
	in to the Pulse administration. (Ie, you should be able to edit it.)

- Did you include {{navigation_logged_in}}? Does it look OK when logged in
	or does it break the template?
- In-line editting *for blocks*: Make sure you can edit a block by 
	clicking the edit box and saving it.
- In-line editting *for pages*: Make sur you can edit a page by clicking the edit for pages.
- Proper saving: Ensure when you save it actually "sticks" and saves the text.
- Proper rendering: Ensure when you edit an H1 it stays that way, for example.


## Mobile
- Mobile navigation: A classic challenge. Ensure the menu button works and that the menu
	is shown/hidden when its clicked.
- Overall mobile look. It should look proper in mobile view.
- Mobile scroll: Check and issues when scrolling on mobile.
- Mobile sub-navigation: Does it show? Does it work?

## Blog pages
	Remember end-customers are often editing their blog every week so the 
	look and feel of blog pages is important, including when editing.
- Ensure main blog page looks OK.
- Logged in: Ensure main blog page looks OK when logged in.
- Blog page scrolling: Ensure when going to page 2 or 3 it looks good. You'll
	need to make multiple blog posts to check this.
- Blog item layout looks good. Should not be broken looking or have weird/bad spacing.
- Individual blog item looks OK. Click into a blog and ensure it looks OK. (Check CSS.)
- Search and tag area: Does it look nice/well design in a proper area with spacing?
	If possible, avoid a design where its randomly dumped like a year 2000 web page.
- Test clicking on the blog tags. Do they work and render nicely?
- Test blog search. Does it work and render nicely?

## Galleries
 - Ensure they render nicely.

## Form: Ensure forms appear and work nicely.
- Default form.
- JustForms form.

