DROP VIEW IF EXISTS `loc_view`;
CREATE view `loc_view` AS
SELECT `locID`, `key`, `type`, `tiny`, `admin`, `locIdx`, `langIdx`, `content`
FROM `loc_text` AS `t`
JOIN `loc_key` AS `k` ON k.`locID` = t.`locIdx`

-- CREATE view `loc_view` AS
-- SELECT *
-- FROM `loc_key` AS `k` , `loc_text` AS `t`
-- WHERE k.`locID` = t.`locIdx`
