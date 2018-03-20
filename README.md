# H E R A L D R Y  E N G I N E

A basic parser and renderer for machine-readable blazon.

## WHAT IS MACHINE-READABLE BLAZON?

It is a subset of the language used to describe coats of arms (blazon) which 
can be parsed by an automated process. The latest (alpha) version of the spec 
can be found in spec.txt, and it is the goal of this project to expand MRB to 
be as large a subset of blazonry proper as is possible. The website contained 
in these files should be regarded as the (very unfinished) reference 
implementation of MRB.

## WHAT IS THE STATE OF THIS PROJECT?

### STATE OF THE PARSER

Currently basic blazons containing a division, an ordinary, charges around the 
ordinary, and charges on the ordinary can be parsed. Divisions can contain 
sub-blazons as complicated as a primary blazon. Movable charges can also be 
specified without an ordinary, in particular semy charges. All movable charges 
can be oriented. Groups of charges can be given an arrangement (eg "in pale"). 
All tinctures (including "counterchanged") can be parsed.

### STATE OF THE RENDERER

For rendering, of the ordinaries only the fess, bend, bend sinister and pale 
can be drawn. Of movable charges, only the mullet, roundel, billet, phrygian 
cap, mullet of six points, fleur-de-lys, lozenge and key can be drawn. The 
divisions per fess, per pale, per bend and per bend sinister can be drawn. All 
metals and colours can be drawn. Of the furs, ermine and ermines can be drawn. 
Charges can be drawn counterchanged. Charges can be drawn on ordinaries, but 
a single charge on a bend shows in the middle instead of in honour point.

### STATE OF THE FRONTEND

Basic access control has been implemented. There are standard users and 
administrators. Administrators can add and remove users, change the password of 
any user, and view the list of users and active sessions. The list of sessions 
is generated without relying on undocumented behaviour.

## WHAT IS NEXT?

I am currently re-writing the parsing code to conform to the newly written 
spec. The re-write is from the ground up, and the new parser will take a much 
more object-oriented style. It will also parse a wider gamut of blazons.

The spec will be updated with a formal grammar soon. It will also be expanded 
slightly to explain what each of the parts mean and how they should be 
rendered, including specifying individual charges and divisions.

I will also add the ability to save blazons, per-user. I may also add 
the ability to refer to "the arms of..." within a blazon, to simplify 
complicated blazons.

For the front-end, rate limiting login attempts is a good idea. And I might add 
2FA with TOTP if I'm feeling churlish. Moving to a properly templated system is 
also probably a good idea.
