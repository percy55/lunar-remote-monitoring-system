/*
canary.c
Monitor the MyCompany Monitoring Facility back-end for any problems at all.
Written by Martin A. COLEMAN (C) 2013. All rights reserved.

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

Watch out for:
* DC insulation fault.
* Residual current faulty
* Zero power for 2 days in a row
* Grid out of range
* Inverter overheating
* Inverter performance is below normal for 2 weeks in a row
*/
#include <stdio.h>
#include "libs/sqlite3.c"

#define MAX_NUM_INVERTERS 10000

typedef struct {
    char serial[10];
    int dc_fault;
    int current_faulty;
    int zero_power;
    int grid_range;
    int invert_heat;
    int invert_perf;
} PROBLEM;

FILE *logfile;

void close_up(void)
{
    fclose(logfile);
    exit(0);
}

int main()
{
    /* general variables */
    FILE *fp;

    /* for sqlite3 */
    sqlite3 *db;
    sqlite3_stmt *res;
    char *zErrMsg=0;
    int rc;
    char sql_str[2048];
    int result=0;

    PROBLEM report[MAX_NUM_INVERTERS];
    printf("L.U.N.A.R. Canary (C) 2013 Martin COLEMAN.\n");
    printf("Opening canary log file...");
    logfile=fopen("/tmp/canary.log", "a");
    if(logfile==NULL)
    {
        printf("Error opening up canary log file.\n");
        close_up();
    }
    atexit(close_up);
    return 0;
}
