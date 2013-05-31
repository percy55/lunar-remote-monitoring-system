/*
    record.c

    Copyright (c) 2013, Martin A. COLEMAN. All rights reserved.

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
#include "qdecoder.h"

int main(void)
{
    sqlite3 *db;
    sqlite3_stmt *res;
    char *zErrMsg=0;
    int rc;
    char sql_str[2048];
    int result=0;

    /* initialise CGI */
    qentry_t *req = qcgireq_parse(NULL, 0);

    /* for return codes */
    printf("Content-type: text/html\n\n");

    /* get inverter information */
    const char *serial = (char *)req->getstr(req, "serial", false);
    if (serial == NULL)
    {
        printf("101");
        return 0;
    }

    sql_str[0]='\0';
    /* open SQLite3 database */
    rc = sqlite3_open("../data/records.sq3", &db);
	if(rc)
	{
		printf("Can't open package database.");
		sqlite3_close(db);
		return 1;
	}
    rc = sqlite3_prepare_v2(db, sql_str, -1, &res, 0);
	if(rc != SQLITE_OK)
	{
		printf("DB Error: %s\n", sqlite3_errmsg(db));
		sqlite3_free(zErrMsg);
		sqlite3_close(db);
		return 1;
	}
	while(1)
	{
		result=sqlite3_step(res);
		if(result==SQLITE_ROW)
		{
		} else {
			break;
		}
	}

    /* free up database */
	sqlite3_finalize(res);
    sqlite3_close(db);

    /* free up CGI */
    req->free(req);
	return 0;
}
