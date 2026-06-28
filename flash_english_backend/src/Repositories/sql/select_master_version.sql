SELECT version_id, version_name, version_no, version_description
FROM master_version
WHERE version_name = :version_name
LIMIT 1;
