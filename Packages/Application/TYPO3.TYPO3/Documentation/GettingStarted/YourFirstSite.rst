=========================================
Creating your first TYPO3 Phoenix website
=========================================

.. note::

	There is also a site kickstart package available to
	help you to kickstart a new website. This guide will help
	you by manually setting up a site.

Create Site Package
===================

In TYPO3 Phoenix your website is basically a TYPO3 Flow package. To create
your website package you use the following command:

::

	./flow package:create My.Site

.. tip::

	It's highly recommended to move your package to the Packages/Sites/ folder.

Now make sure you have the `type` set to `typo3-flow-site` in your `composer.json` file.

Create basic site structure
===========================

To create an empty site structure we create a `Resources/Private/Content/Sites.xml` file
with the following content:

.. code-block:: xml

	<?xml version="1.0" encoding="UTF-8"?>
	<root>
		<site nodeName="mysite">
			<properties>
				<name>My Site</name>
				<state>1</state>
				<siteResourcesPackageKey>My.Site</siteResourcesPackageKey>
			</properties>
			<node identifier="" type="TYPO3.Phoenix.ContentTypes:Page" nodeName="homepage" locale="en_EN">
				<properties>
					<title>Home</title>
				</properties>
				<node identifier="" type="TYPO3.Phoenix.ContentTypes:Section" nodeName="main" locale="en_EN">
					<node identifier="" type="TYPO3.Phoenix.ContentTypes:Text" nodeName="text1" locale="en_EN">
						<properties>
							<headline>Welcome</headline>
							<text><![CDATA[This is your first TYPO3 Phoenix website.]]>	</text>
						</properties>
					</node>
				</node>
			</node>
		</site>
	</root>

Now import this basic structure using the following command:

::

	./flow site:import --packageKey My.Site

Adding a template
=================

Now create the file `Resources/Private/TypoScripts/Library/Root.ts2`, and add the following content:

::

	page = Page
	page.body {
		templatePath = 'resource://My.Site/Private/Templates/Page/Default.html'
		sectionName = 'body'

		sections.main = TYPO3.TYPO3:Section
		sections.main.nodePath = 'main'
	}

This includes all default TypoScript from TYPO3 Phoenix and creates a `TYPO3.Phoenix.ContentTypes:Page`
object. Besides that we set the template, and configure we will use the 'body' section
from this template. The last two lines add actual content rendering to the page for all
content in the 'main' section node (see Sites.xml file).

Now create your HTML template in `Resources/Private/Templates/Page/Default.html`, add
something like this:

.. code-block:: html

	<!DOCTYPE html>
	{namespace typo3=TYPO3\TYPO3\ViewHelpers}
	{namespace ts=TYPO3\TypoScript\ViewHelpers}
	<html>
		<head>
			<meta charset="UTF-8" />
			<f:base />
			<title>My Site Template</title>
		</head>
		<body>
			<f:section name="body">
				<div class="t3-reloadable-content">
					<header>
						<h1>My Site</h1>
					</header>
					<div id="mainContent">
						<ts:renderTypoScript path="sections/main" />
					</div>
				</div>
			</f:section>
		</body>
	</html>
