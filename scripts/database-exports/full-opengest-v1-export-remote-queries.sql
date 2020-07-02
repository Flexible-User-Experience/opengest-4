-- Enterprise

SELECT E.*
INTO OUTFILE '/var/lib/mysql-files/enterprises.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Empresas E;

SELECT TDC.*
INTO OUTFILE '/var/lib/mysql-files/enterprise_collection_document_types.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tipos_documentos_cobro TDC;

SELECT GP.*, E.cif_nif
INTO OUTFILE '/var/lib/mysql-files/enterprise_group_bountys.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Grupos_primas GP
JOIN opengest.Empresas E ON E.id = GP.empresa_id;

SELECT DF.*, E.cif_nif
INTO OUTFILE '/var/lib/mysql-files/enterprise_holidays.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Dias_festivos DF
JOIN opengest.Empresas E ON E.id = DF.empresa_id;

SELECT CT.*, E.cif_nif
INTO OUTFILE '/var/lib/mysql-files/enterprise_transfer_accounts.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Cuentas_transferencia CT
JOIN opengest.Empresas E ON E.id = CT.empresa_id;

SELECT LA.*, E.cif_nif
INTO OUTFILE '/var/lib/mysql-files/enterprise_activity_lines.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Lineas_actividad LA
JOIN opengest.Empresas E ON E.id = LA.empresa_id;

-- Operator

SELECT O.*, E.cif_nif
INTO OUTFILE '/var/lib/mysql-files/operators.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Operarios O
JOIN opengest.Empresas E ON E.id = O.empresa_id;

SELECT TRO.*
INTO OUTFILE '/var/lib/mysql-files/operator_checking_types.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tipos_revisiones_operario TRO;

SELECT RO.*, TRO.nombre, O.dni
INTO OUTFILE '/var/lib/mysql-files/operator_checkings.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Revisiones_operario RO
JOIN opengest.Tipos_revisiones_operario TRO ON TRO.id = RO.tipos_revision_operario_id
JOIN opengest.Operarios O ON O.id = RO.operario_id;

SELECT TA.*
INTO OUTFILE '/var/lib/mysql-files/operator_absence_types.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tipos_ausencias TA;

SELECT AO.*, TA.nombre, O.dni
INTO OUTFILE '/var/lib/mysql-files/operator_absences.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Ausencias_operario AO
JOIN opengest.Tipos_ausencias TA ON TA.id = AO.tipo_ausencia_id
JOIN opengest.Operarios O ON O.id = AO.operario_id;

SELECT TacO.*, O.dni
INTO OUTFILE '/var/lib/mysql-files/operator_digital_tachographs.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tacografos_operarios TacO
JOIN opengest.Operarios O ON O.id = TacO.operario_id;

SELECT IV.*, O.dni
INTO OUTFILE '/var/lib/mysql-files/operator_various_amounts.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Importes_varios IV
JOIN opengest.Operarios O ON O.id = IV.operario_id;

-- Partner

SELECT CT.*
INTO OUTFILE '/var/lib/mysql-files/partner_classes.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Clases_terceros CT;

SELECT TT.*
INTO OUTFILE '/var/lib/mysql-files/partner_types.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tipos_terceros TT;

SELECT T.*, E.cif_nif AS E_cif_nif, TT.nombre AS TT_nombre, CT.nombre AS CT_nombre, CUTR.nombre AS CUTR_nombre
INTO OUTFILE '/var/lib/mysql-files/partners.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Terceros T
JOIN opengest.Empresas E ON E.id = T.empresa_id
JOIN opengest.Tipos_terceros TT ON TT.id = T.tipo_tercero_id
JOIN opengest.Clases_terceros CT ON CT.id = T.clase_tercero_id
LEFT JOIN opengest.Cuentas_transferencia CUTR ON CUTR.id = T.cuenta_transferencia_id;

SELECT C.*, T.cif_nif AS T_cif_nif, E.cif_nif AS E_cif_nif
INTO OUTFILE '/var/lib/mysql-files/partner_contacts.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Contactos C
JOIN opengest.Terceros T ON T.id = C.tercero_id
JOIN opengest.Empresas E ON E.id = T.empresa_id;

SELECT DI.*, T.cif_nif AS T_cif_nif, E.cif_nif AS E_cif_nif
INTO OUTFILE '/var/lib/mysql-files/partner_unabled_days.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Dias_inhabiles DI
JOIN opengest.Terceros T ON T.id = DI.tercero_id
JOIN opengest.Empresas E ON E.id = T.empresa_id;

SELECT O.*, T.cif_nif AS T_cif_nif, E.cif_nif AS E_cif_nif
INTO OUTFILE '/var/lib/mysql-files/partner_building_sites.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Obras O
JOIN opengest.Terceros T ON T.id = O.tercero_id
JOIN opengest.Empresas E ON E.id = T.empresa_id;

SELECT P.*, T.cif_nif AS T_cif_nif, E.cif_nif AS E_cif_nif
INTO OUTFILE '/var/lib/mysql-files/partner_orders.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Pedidos P
JOIN opengest.Terceros T ON T.id = P.tercero_id
JOIN opengest.Empresas E ON E.id = T.empresa_id;

-- Sale

SELECT T.*, E.cif_nif AS E_cif_nif
INTO OUTFILE '/var/lib/mysql-files/sale_tariff.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tarifas T
JOIN opengest.Empresas E ON E.id = T.empresa_id;

SELECT F.*, S.nombre AS S_nombre, T.cif_nif AS T_cif_nif, E.cif_nif AS E_cif_nif
INTO OUTFILE '/var/lib/mysql-files/sale_invoice.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Facturas F
JOIN opengest.Serie S ON S.id = F.serie_id
JOIN opengest.Terceros T ON T.id = F.tercero_id
JOIN opengest.Empresas E ON E.id = T.empresa_id
ORDER BY F.id;

SELECT A.*, LA.nombre AS LA_nombre, TDC.nombre AS TDC_nombre, P.num_pedido AS P_num_pedido, T.cif_nif AS T_cif_nif, E.cif_nif AS E_cif_nif, O.nombre AS O_nombre
INTO OUTFILE '/var/lib/mysql-files/sale_delivery_note.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Albaranes A
LEFT JOIN opengest.Lineas_actividad LA ON LA.id = A.linea_actividad_id
LEFT JOIN opengest.Tipos_documentos_cobro TDC ON TDC.id = A.tipo_documento_cobro_id
LEFT JOIN opengest.Pedidos P ON P.id = A.pedido_id
LEFT JOIN opengest.Terceros T ON T.id = A.tercero_id
LEFT JOIN opengest.Empresas E ON E.id = A.empresa_id
LEFT JOIN opengest.Obras O ON O.id = A.obra_id;

-- Setting

SELECT S.*
INTO OUTFILE '/var/lib/mysql-files/setting_sale_invoice_series.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Serie S;

-- Vehicle

SELECT TR.*
INTO OUTFILE '/var/lib/mysql-files/vehicle_checking_types.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Tipos_revision TR;

SELECT R.*, TR.nombre, V.matricula
INTO OUTFILE '/var/lib/mysql-files/vehicle_checkings.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '\\'
LINES TERMINATED BY '\n'
FROM opengest.Revisiones R
JOIN opengest.Tipos_revision TR ON TR.id = R.tipo_revision_id
JOIN opengest.Vehiculos V ON V.id = R.vehiculo_id;
