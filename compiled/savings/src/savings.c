#include "includes/savings.h"

double      calculateSavings(double target, double interest, int period)
{
    double  amount;
    double  x;
   
    x = (period * interest) + period;
    amount = target / x;
    return amount;
}

double      totalInvestment(double amount, double interest, int period, char *on_interest)
{
    double  total_invested;
    
    if (strcmp(on_interest, "no") == 0) //If you don't earn interest on interest
        total_invested = period * ((amount * interest) + amount);
    else if (strcmp(on_interest, "yes") == 0) //If you earn interest on interest
        total_invested = period * ((amount * interest) + (amount * interest * interest) + amount);
    return total_invested;
}