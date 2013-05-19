#!/bin/sh

# gcc -w -Os -ansi -DSQLITE_OMIT_LOAD_EXTENSION -DSQLITE_DISABLE_LFS -DSQLITE_THREADSAFE=0 -o record.cgi -Ilibs/qdecoder/src/ ../receiver/record.c libs/qdecoder/src/libqdecoder.a
# gcc -w -Os -ansi -DSQLITE_OMIT_LOAD_EXTENSION -DSQLITE_DISABLE_LFS -DSQLITE_THREADSAFE=0 -o calc_kwh calc_kwh.c
# gcc -w -Os -ansi -DSQLITE_OMIT_LOAD_EXTENSION -DSQLITE_DISABLE_LFS -DSQLITE_THREADSAFE=0 -o canary canary.c

tcc -w -DSQLITE_OMIT_LOAD_EXTENSION -DSQLITE_DISABLE_LFS -DSQLITE_THREADSAFE=0 -o record.cgi -Ilibs/qdecoder/src/ ../receiver/record.c libs/qdecoder/src/libqdecoder.a
tcc -w -DSQLITE_OMIT_LOAD_EXTENSION -DSQLITE_DISABLE_LFS -DSQLITE_THREADSAFE=0 -o calc_kwh calc_kwh.c
tcc -w -DSQLITE_OMIT_LOAD_EXTENSION -DSQLITE_DISABLE_LFS -DSQLITE_THREADSAFE=0 -o canary canary.c
