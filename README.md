# 100tb-whmcs-module
100TB WHMCS SSD VPS Provisioning Module. Built on WHMCS v6.02

#installation instructions
##Step 1

Connect to your server via ssh download the master file into the modules directory.

Navigate to /var/www/html/whmcs/modules/servers/ and run the following command:
'wget https://github.com/UK2group/100tb-whmcs-ssdvps/archive/master.zip'

Once downloaded go ahead and unzip the file.
'unzip master.zip'

once unziped rename the directory to ssd100tb with the following command:
'mv 100tb-whmcs-ssdvps-master/ ssd100tb/'

##Step 2
Log in to your servers whmcs admin page. http://example.com/whmcs/admin/index.php

Once logged in go setup -> products/services -> products/services

Select "Create a New Group". select order form template and name product group something like "SSD-VPS" and save changes.

Now select "Create a New Product". Product type = "Dedicated/VPS Server", Product Group = "SSD-VPS". Name product something like "Tier-1".

##Step 3
You should now be at http://example.com/whmcs/admin/configproducts.php

Go ahead and edit "Tier-1"

In the details tab you can select all the options you need. The Product Description can be based on your module SSD VPS Plan.
Go to the module settings tab. Select "100TB SSD VPS" from the modules drop down list.
Now select from the options available. Make sure you insert a valid API key from your 100tb account. https://console.100tb.com/#/tools/api and hit save changes.
