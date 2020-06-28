

help:
	echo Specifie a target please!

data-backup: 
	$(MAKE) data/structure.sql
	cd data && git commit structure.sql -m "Autobackup $$(date)" && git push

.PHONY: data/structure.sql
data/structure.sql:
	mysqldump --no-data --skip-dump-date --user cartons cartons | tee $@
