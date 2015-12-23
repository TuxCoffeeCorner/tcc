# README
## About TCC
TCC is a self service kiosk system. We developed it for use in our coffee corner. It is a sort of electronic tally sheet. Customers scan the bar code of products they'd like to buy and the system keeps track of their account balance accordingly.

## Payment process
### Identification
First, the kiosk system needs to know who is in need of coffee. In our case, we solved this problem using a bar code scanner. Every employee of our company is in the possession of an id card and a personnel number. This unique number is printed as a bar code on the id card. Using the bar code scanner, we are able to identify the customer via LDAP. So our customer scans in his barcode first.

### Purchase
The identified customer can then buy the article(s) he wants by scanning the bar code from the products' packaging.

### Completion
The customer can either simply wait/walk away (there's a timeout) or scan a special bar code that finishes the payment process.

## New customers
New customers aren't any different from existing ones, with the exception of the already collected information and the account balance. If a customer scans in his personnel number and the kiosk system can't find it in the internal database, it is going to collect his name and mail address via LDAP from a pre-configured LDAP directory. This information is then stored in the database including an initial account balance of 0.00 CHF (we're a Swiss company and haven't yet made it possible to change the currency, sorry).

## Administrative tasks
### Credit an account
As soon as an account has been created in the kiosk system, it can be credited. The only possibility to credit one's account is to give money to an admin and hope he doesn't forget it. It is possible to generate debt, though... The admin does the charging in the admin interface.

### Register products
In order for the kiosk system to know what the customer wants to buy, the products need to be registered. This is done via the admin interface. The admin defines name, bar code number, picture and price of the product.

## Prerequisites
These can be seen as a guideline rather than hard prerequisites. There is a ton of different ways how to use this system.
- Client: A laptop or PC with a GUI and an installed web browser.
- Server: A PHP webserver serving the TCC website.
- Identifier: Costumers need a bar code representing a unique identifier which can also be found in an LDAP directory.
- Bar code scanner
- Backup disk or thumb drive: This is optional as the system works without a backup drive, but should be done in every productive scenario.

### Example setup
We had client and server on the same laptop which stood in our coffee corner. It ran Debian Jessie with Gnome and an Apache (prefork) webserver with mod_php. We used two different USB thumb drives attached to the laptop for backup and of course a bar code scanner.

## Further information
- SETUP.md
