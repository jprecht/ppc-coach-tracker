<?php
$current_menu_item = "quick_start";
include("config.php");
include("header.php");
?>
<div id="content-wrap">
	<div id="content">
		<div style="width:760;align:center;">		
			<h1>Quick Start</h1>
			
			<p><span style="background-color: #EEF7DB">Basic Mechanics</span></p>			
			
			<p class="gbox">The program is designed to handle all your tracking needs right down to the second a keyword or adgroup converts.  Instead of forcing you to input all the information ahead of time, (which is tedious and annoying), you send the info through the destination url for the ad or keyword from the ppc engine to the script.  The script then logs that click, assigns a single id value to it and allows you to pass the id value onto the link to the offer or to your redirect page or wherever you want to send it.  Now, if the affiliate network registers a sale for you, the only thing they will see is a number.  So your keywords are totally hidden from them.<BR><BR>The nice part is to update your stats, you just need to upload the "optional info" report from the network and then the ppc engine cost report.  The system will then calculate everything for you.  You can view all your stats through the "Reports" link.</p>
			
			<p><span style="background-color: #EEF7DB">How do I use a.php?</span></p>
			
			<p class="gbox">
				This is where you will send all your clicks from the ppc engines.  Your destination url or keyword destination url will look like this:<BR><BR>
				<?php
				$str = "http://www.your-tracking-domain.com/a.php?campaign={campaign}&adgroup={adgroup}&keyword={keyword}&match_type={match_type}&ad={creative}&website={placement}&network=content&source=adwords&z=4&b=1";
				echo chunk_split($str,100);
				?><BR><BR>
				<B>Sending Clicks to Your Landing Page and Rotating Offers:</B>
				<BR><BR>Now you can fill those values in manually, but I do not believe in creating more work, SO the generators have all been modified to fill these values in for you and then convert them to url encoded values so there will be no problem entering them into any ppc engine.<BR><BR>The "z" variable is the need to manually input and it is the number that represents the landing page url.  You input all your landing pages under the "Manager" section.  Then a.php will send all clicks to the landing page you want based on that number.  The url you will enter into the landing page url on the manager page is like this:<BR><BR>http://www.your-landing-page.com/index.php<BR><BR>Which will then produce this link for you automatically:<BR><BR>http://www.your-landing-page.com/index.php?a=$hidden_id&b=$b<BR><BR>To explain that link, "a" is now the id number of the click.  It is the value that will be passed onto the network.  "b" is the number that represents which offer OR set of rotating offers you want the click to go to.  (This too is set up on the manager page).  The whole point is to take that big long incoming url, enter it into your kw_log table and then give you an id number that represents it.  I take it a step further by allowing you to then rotate back end offer packages easily by setting the "b" variable to your desired "offer package".
				<BR><BR>
				<B>Direct Linking Setup:</B>
				<BR><BR>There is one more thing about the "b" variable.  If you want to do direct linking, (ie send the surfer straight to the merchants site without any rotation or using your own landing page), then set b=0, (b equals zero).  In this case only, you "z" variable is going to be the offer id INSTEAD OF the landing page id.  So you do not need to enter a landing page in the manager area in this case, you would only need to enter the offer information in the offer section and use that id as the "z" variable.
				<BR><BR>
				<B>Iframe Setup:</B>
				<BR><BR>
				To use the system with an iframe, you need to set up your landing page as an iframe.  Then as the url inside the landing page you would but the b.php link.  This will rotate your offers within the landing page.  So, a.php will point to your iframe landing page, (which you will enter under landing pages), then in your landing page code, you would put the b.php link.
			</p>
			 
			<p><span style="background-color: #EEF7DB">What is b.php for?</span></p>
			
			<p class="gbox">
				This file is used to log which offer the click went to.  It is the outgoing link on your landing page for all your clicks.  So, a.php sends the user to your landing page and b.php is used as the outgoing link on your landing page.  It updates the click with the offer information, so you know exactly where the click went.
			</p>
			
			<p><span style="background-color: #EEF7DB">How do I set this up?</span></p>
			
			<p class="gbox">
				Now that you know how the two main files work and what they do, you can set up your campaign(s).  Here are the steps to take:
				<ol>
					<li>Do your research as normal, gather your offers and keywords etc.</li>
					<li>Enter your offer rotation package, also under the "Manager" tab.  If you don't want to rotate any offers, you don't have to, just enter one offer instead of several.</li>
					<li>Enter your landing pages into the tracker script under the "Manager" tab, then tell the script which offer package you want it to run with that landing page.</li>
					<li>Set up your campaign.  I would use the generators on the ppc-coach.com site for this as it makes short work of it, but you could do it manually if you wanted.  Using the generators, just enter all the values as normal, but for your destination urls, use the format in the link example under the a.php section above.</li>
					<li>Each day enter yesterdays stats from the ppc engine report and the network "optional info" report.</li>
					<li>Manage your campaigns by using the "Reports" tab.</li>
					<li>Sit back in amazement at how rock solid this system is and send PPC Coach beer and wine as a thank you... :)</li>
				</ol>
			</p>
			 
			<p><span style="background-color: #EEF7DB">Where do I go for help?</span></p>
			
			<p class="gbox">
				Go to the ppc-coach.com support forum for help.  I will also be producing several video tutorials to help you get up and running quickly.
			</p>
		</div>
<?php
include("footer.php");
?>