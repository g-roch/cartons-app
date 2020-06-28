

help:
	echo Specifie a target please!

data-backup: 
	$(MAKE) data/structure.sql
	$(MAKE) data/data.sql
	cd data && git commit structure.sql data.sql -m "Autobackup $$(date)" && git push

.PHONY: data/structure.sql
data/structure.sql:
	mysqldump --no-data --skip-dump-date --user cartons cartons | tee $@

.PHONY: data/data.sql
data/data.sql:
	mysqldump --no-create-info --skip-dump-date --user cartons cartons | tee $@
