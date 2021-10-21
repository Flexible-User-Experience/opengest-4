#!/bin/bash

php bin/console  app:import:province var/csv/partners.csv 9 10
php bin/console  app:import:province var/csv/partners.csv 19 20
php bin/console  app:import:province var/csv/enterprises.csv 5 6
php bin/console  app:import:province var/csv/operators.csv 12 12
php bin/console  app:import:city var/csv/partners.csv 8 6 9 10
php bin/console  app:import:city var/csv/partners.csv 18 17 19 20
php bin/console  app:import:city var/csv/enterprises.csv 4 7 5 6
php bin/console  app:import:city var/csv/operators.csv 11 10 12 12
php bin/console  app:import:enterprise var/csv/enterprises.csv
php bin/console  app:import:enterprise:activity:line var/csv/enterprise_activity_lines.csv
php bin/console  app:import:enterprise:collection:document:type var/csv/enterprise_collection_document_types.csv
php bin/console  app:import:enterprise:group:bounty var/csv/enterprise_group_bountys.csv
php bin/console  app:import:enterprise:holiday var/csv/enterprise_holidays.csv
php bin/console  app:import:enterprise:transfer:account var/csv/enterprise_transfer_accounts.csv
php bin/console  app:import:operator var/csv/operators.csv
php bin/console  app:import:operator:checking:type var/csv/operator_checking_types.csv
php bin/console  app:import:operator:checking var/csv/operator_checkings.csv
php bin/console  app:import:operator:absence:type var/csv/operator_absence_types.csv
php bin/console  app:import:operator:absence var/csv/operator_absences.csv
php bin/console  app:import:operator:digital:tachograph var/csv/operator_digital_tachographs.csv
php bin/console  app:import:operator:various:amount var/csv/operator_various_amounts.csv
php bin/console  app:import:partner:class var/csv/partner_classes.csv
php bin/console  app:import:partner:type var/csv/partner_types.csv
php bin/console  app:import:partner var/csv/partners.csv
php bin/console  app:import:partner:contact var/csv/partner_contacts.csv
php bin/console  app:import:partner:unable:days var/csv/partner_unabled_days.csv
php bin/console  app:import:partner:building:site var/csv/partner_building_sites.csv
php bin/console  app:import:partner:order var/csv/partner_orders.csv
php bin/console  app:import:setting:sale:invoice:series var/csv/setting_sale_invoice_series.csv
php bin/console  app:import:sale:tariff var/csv/sale_tariff.csv
php bin/console  app:import:sale:invoice var/csv/sale_invoice.csv
php bin/console  app:create:sale:items
php bin/console  app:create:time:ranges
php bin/console  app:create:payslip:line:concepts
