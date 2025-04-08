Changelog
=========

##### Version 4.1.23 (WIP)
* Keep wiring things

##### Version 4.1.22 (2025-04-08)
* Added tax type to partner and included in eInvoice if partner has it set.

##### Version 4.1.21 (2025-03-06)
* Add iban and bic in eInvoice payment generation.
* Add PartyIdentification to buyer in eInvoice.

##### Version 4.1.20 (2025-02-24)
* Specify due dates in eFactura generation and detail discounts.

##### Version 4.1.19 (2025-02-10)
* Set correct quantity to eFactura invoice lines.
* Save e-invoice xml document to specified environment folder parameter when sale invoice has been
counted.

##### Version 4.1.18 (2025-02-07)
* Added payment method if exists in eFactura generation.

##### Version 4.1.17 (2025-01-27)
* Added billing period in eFactura generation (currently same as invoice date).

##### Version 4.1.16 (2025-01-23)
* Changes in eFactura service to integrate with external e-invoice service.

##### Version 4.1.15 (2024-09-27)
* Upgrade to Symfony 6.4 LTS.
* Set hour to holiday hour in operatorWorkRegisters if it is a weekend or holiday.
* Split totals in payslip: total accrued, total deductions and total amount (as total liquid). Changed this total
calculations based on if payslip lines is deduction or not.

##### Version 4.1.14 (2024-07-12)
* Edit logbook pdf to show by cost center
* Edit vehicle maintenance pdf to show last revision date

##### Version 4.1.13 (2024-06-19)
* Add logbook to vehicle
* Fix long invoice pdf generation

##### Version 4.1.12 (2024-02-19)
* Be able to filter vehicle maintenance when exporting to pdf.
* Can delete sale delivery note, orphaning operator work registers and setting their delivery note to null.

##### Version 4.1.11 (2024-01-30)
* Upgraded to php8.3.

##### Version 4.1.10 (2024-01-15)
* Upgraded composer packages.
* Upgraded to php8.2.
* Fixed error in vehicle maintenance pdf generation.
* Improved purchase invoice lines view.

##### Version 4.1.09 (2023-07-31)
* Fixed error with analytics, handle inactive clients

##### Version 4.1.08 (2023-07-03)
* One postal code can have two different name in City.
* In custom invoice generation screen, when filtered by partner, only related BuildingSites
and Orders are shown.

##### Version 4.1.07 (2023-06-16)
* In Sale Request and Sale Delivery Note, when filtered by partner, only related BuildingSites 
and Orders are shown. 

##### Version 4.1.06 (2023-02-27)
* Added price in Vehicle fuel, and recalculated vehicle consumptions amount from this price
* Added discounts in purchase invoice lines

##### Version 4.1.05 (2022-12-30)
* Added operator checking training and ppe related to operators

##### Version 4.1.04 (2022-12-01)
* update ansible 
* add document collection in operator vehicle and company
* upgrade vichUploader
* filter client by commercial name

##### Version 4.1.03 (2022-11-28)
* add operatorAbsences in operator
* show tonnage on deliveryNotesToInvoice

##### Version 4.1.02 (2022-11-15)
* update yarn dependencies
* edit accounting account length

##### Version 4.1.01 (2022-10-11)
* add Javascript to speed up business intelligence panel
* update bump loader-utils from 2.0.2 to 2.0.3

##### Version 4.1.00 (2022-09-26)
* add business intelligence panel
* add electronic invoice generation
* add cost centers management

##### Version 4.0.01 (2022-08-28)
* add payment control system

##### Version 4.0.00 with KitDig features (2022-08-20)
* add purchase invoice management
* add article management
* add cost centers management
* include provider management
* include article management

##### Version 3.3.00 (2022-07-21)
* upgrade to symfony/webpack-encore 3 and less11
* update symfony web profiler bundle config
* update symfony validator config
* update symfony twig recipe
* update translations recipe
* update security recipe
* update phpunit monolog routing recipes
* update sonata to 4.14
* update php8
* update ansible deploy to php8.1

##### Version 3.2.00 (2021-04-10)
* include fuel registers
* edit client info
* edit maintenance procedure

##### Version 3.1.00 (2021-01-15)
* update exports and pdf generation to adjust to company

##### Version 3.0.00 (2020-12-23)
* system prepared to migrate to updated version

##### Version 2.1.00 (2020-08-01)
* change request-deliverynote-invoice flux

##### Version 2.0.00 (2020-08-01)
* update Symphony to 4.5
* update Sonata 4

##### Version 1.1.10 (WIP)
 * apply new privacy policy
 
##### Version 1.1.09 (2018-03-29)
 * add more export old database SQLs
 * add more import commands to new database
 * add admin translations
 * fix frontend minor typo in homepage
 * enable admin reset password
 * load sale invoices
 
##### Version 1.1.08 (2019-02-22)
 * add export old database SQLs
 * add import commands to new database
 * check ansible integration with remote exports
 * fix required user profile image problem
 * show sale requests in dashboard

##### Version 1.1.07 (2019-02-01)
 * improve admin dashboard view
 * refactor project folder structure (entities, repositories, admins)
 * add more commands to import old database records
 * define a PDF print service

##### Version 1.1.06 (2019-01-03)
 * fix minor textarea resize bug
 * fix minor invalid Doctrine mapping
 * add auto numbering system into sale delivery notes and sale invoices
 * apply some auto calculations and fix minor bugs into sale delivery notes
 * fix partner unable days date range validation
 * improve dynamic behaviour into sale request views

##### Version 1.1.05 (2018-12-03)
 * add partner unable days management
 * add operator various amount management
 * add sale request delivery note management
 * add invoice manager
 * apply some dynamic behaviour into sale request views

##### Version 1.1.04 (2018-11-05)
 * add activity line management
 * add collection document type management
 * add sale delivery note management
 * add sale delivery note line management
 * add sale invoice management
 * add sale invoice series management

##### Version 1.1.03 (2018-10-01)
 * add enterprise holidays management
 * add sale tariff management
 * add sale request management

##### Version 1.1.02 (2018-09-04)
 * add partner class management
 * add partner type management
 * add partner order management
 * add partner building site management
 * add partner contact management

##### Version 1.1.01 (2018-08-02)
 * add operator digital tachograf management
 * add vehicle digital tachograf management
 * add enterprise group bounty management
 * add enterprise transfer account management
 * add partner class management
 * add partner type management

