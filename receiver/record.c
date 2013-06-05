/*
    record.c
    Copyright (c) 2013, Martin A. COLEMAN. All rights reserved.
    Part of the LUNAR remote monitoring system.
    Compile with gcc -o record.cgi record.c -lsqlite3 -lcrypto

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
#include <string.h>
#include <openssl/sha.h>
#include <sqlite3.h>
#include "qdecoder.h"

int main(void)
{
    sqlite3 *db;
    sqlite3_stmt *res;
    char *zErrMsg=0;
    int rc;
    char sql_str[1024];
    int result=0;
    unsigned char digest[SHA_DIGEST_LENGTH];

    /* initialise CGI */
    qentry_t *req = qcgireq_parse(NULL, 0);

    /* get inverter information */
    const char *serial = (char *)req->getstr(req, "serial", false);
    const char *auth = (char *)req->getstr(req, "auth", false);
    const char *ac_volts = (char *)req->getstr(req, "ac_volts", false);
    const char *ac_current = (char *)req->getstr(req, "ac_current", false);
    const char *ac_freq = (char *)req->getstr(req, "ac_freq", false);
    const char *heatsink_temp = (char *)req->getstr(req, "heatsink_temp", false);
    const char *pv1_volts = (char *)req->getstr(req, "pv1_volts", false);
    const char *pv2_volts = (char *)req->getstr(req, "pv2_volts", false);
    const char *pv1_cur = (char *)req->getstr(req, "pv1_cur", false);
    const char *pv2_cur = (char *)req->getstr(req, "pv2_cur", false);
    const char *watts = (char *)req->getstr(req, "watts", false);

    /* for return codes */
    printf("Content-type: text/html\n\n");
    if(serial[0]==NULL) /* no serial number? */
    {
        printf("ES1");
        req->free(req);
        return 0;
    }
    if(auth[0]==NULL) /* no SHA1 authentication token? */
    {
        printf("ET1");
        req->free(req);
        return 0;
    }
    /* are we authorised? */
    SHA1((unsigned char*) &serial, strlen(serial), (unsigned char*) &digest);
    if(strcmp(digest, auth)) /* if the auth does not match an MD5'ed serial number */
    {
        printf("ET2");
        req->free(req);
        return 0;      
    }

    sql_str[0]='\0';
    sprintf(sql_str, "INSERT INTO readings(inverter, recordedat, ac_volts, ac_current, ac_freq, heatsink_temp, pv1_volts, pv2_volts, pv1_current, pv2_current, watts) VALUES('%s', datetime('NOW'), %s, %s, %s, %s, %s, %s, %s, %s, %s)", serial, ac_volts, ac_current, ac_freq, heatsink_temp, pv1_volts, pv2_volts, pv1_cur, pv2_cur, watts);
    /* open SQLite3 database */
    rc = sqlite3_open("../data/records.sq3", &db);
	if(rc)
	{
		printf("Can't open records database.");
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
    printf("OK");

    /* free up database */
	sqlite3_finalize(res);
    sqlite3_close(db);

    /* free up CGI */
    req->free(req);
	return 0;
}
