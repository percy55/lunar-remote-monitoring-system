/*
    calc_kwh.c
    Copyright (C) 2013 Martin A. COLEMAN. All rights reserved.

    Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met: 

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer. 
	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution. 

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
#include <stdio.h>
#include "libs/sqlite3.c"

/* modify these as needed. could even make them command line parameters */
#define RECORDS_FILE "../data/records.sq3"
#define JAVASCRIPT_FILE "../website/kwg.js"

int main(void)
{
	FILE *fp;
    sqlite3 *db;
    sqlite3_stmt *res;
    char *zErrMsg=0;
    int rc;
    char sql_str[2048];
    int result=0;
    unsigned int total_kwh=0;
    unsigned int temp_total_kwh=0;
    char watts[5];
    unsigned long int num_records=0;

    printf("Opening %s\n", JAVASCRIPT_FILE);
	fp=fopen(JAVASCRIPT_FILE, "w");
	if(fp==NULL)
	{
		printf("Error writing to JS file.\n");
		return 1;
	}
        sql_str[0]='\0';
    /* open SQLite3 database */
    printf("Opening %s\n", RECORDS_FILE);
    rc = sqlite3_open(RECORDS_FILE, &db);
	if(rc)
	{
		printf("Can't open package database.");
        fclose(fp);
		sqlite3_close(db);
		return 1;
	}
    sprintf(sql_str, "SELECT watts FROM readings");
    rc = sqlite3_prepare_v2(db, sql_str, -1, &res, 0);
	if(rc != SQLITE_OK)
	{
		printf("DB Error: %s\n", sqlite3_errmsg(db));
		sqlite3_free(zErrMsg);
		sqlite3_close(db);
		return 1;
	}
    printf("Processing");
	while(1)
	{
        watts[0]='\0';
		result=sqlite3_step(res);
		if(result==SQLITE_ROW)
		{
            sprintf(watts, "%s", sqlite3_column_text(res, 0));
            temp_total_kwh+=atoi(watts);
            putchar('.');
            num_records++;
		} else {
			break;
		}
	}
    printf("\nFinished.\n");
    /* free up database */
	sqlite3_finalize(res);
    sqlite3_close(db);

    total_kwh=temp_total_kwh/1000;
    printf("Total Kw/h %u\n", total_kwh);
    printf("No. of records: %u\n", num_records);
    /* write the JS file */
    fprintf(fp, "function show_kwg() { var total_kwh=%u; document.write(numberWithCommas(total_kwh)); }\n", total_kwh);
    fclose(fp);
	return 0;
}
