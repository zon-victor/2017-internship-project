
#ifndef SAVINGS_H
# define SAVINGS_H

# include <stdlib.h>
# include <string.h>
# include <stdio.h>

double      calculateSavings(double target, double interest, int period);
double      totalInvestment(double amount, double interest, int period, char *on_interest);

#endif