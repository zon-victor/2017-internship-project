#include "includes/savings.h"

int     main(int ac, char **av)
{
    double  amount;
    double  target;
    double  monthly_investment;
    double  interest_rate;
    int     period;//This will be in months even though the user may use years
    char    *years;
    char    *months;
    char    *interest_period;//monthly or yearly or none
    char    *type;//goal or investment
    double  interest;
    char    *on_interest;
    
    if (ac != 7 && ac != 8) {
        printf("Usage Example: ./savings type years months interest target/monthly_investment interest_period interest_on_interest\n");
        printf("Target Example (No interest on interest): ./savings target y1 m12 10 5400 yearly\n");
        printf("Term Example (Optional interest on interest): ./savings term y1 m12 10 5400 (monthly/yearly) (yes/no)\n");
        return (0);
    }
    
    type = av[1];
    years = strchr(av[2], 'y') + 1;
    months = strchr(av[3], 'm') + 1;
    period = (atoi(years) * 12) + atoi(months);
    interest_rate = atof(av[4]);
    if (strcmp(type, "target") == 0)
        target = atof(av[5]);
    else if (strcmp(type, "term") == 0) {
        monthly_investment = atof(av[5]);
        on_interest = av[7];
    } else {
        printf("Savings type should be target or term, nothing else\n");
        return 0;
    }
    interest_period = av[6];
    
    if (strcmp(type, "target") == 0) //Find out how much you must save every month for you reach a goal
    {
        if (strcmp(interest_period, "none") == 0)
        {
            amount = target / period;
        } else if (strcmp(interest_period, "monthly") == 0) {
            interest = interest_rate / 100;
            amount = calculateSavings(target, interest, period);
        } else if (strcmp(interest_period, "yearly") == 0) {
            interest = (interest_rate / 100) / 12;
            amount = calculateSavings(target, interest, period);
        }
        if (period == 0) {
            amount = target;
            printf("%.2f \n" , amount);
        } else {
            printf("%.2f \n" , amount);
        }
    } else if (strcmp(type, "term") == 0) {//Calculate how much you will have after a period of time if you save a certain amount on monthly basis
        if (strcmp(interest_period, "none") == 0)
        {
            amount = target / period;
        } else if (strcmp(interest_period, "monthly") == 0) {
            interest = interest_rate / 100;
            amount = totalInvestment(monthly_investment, interest, period, on_interest);
        } else if (strcmp(interest_period, "yearly") == 0) {
            interest = (interest_rate / 100) / 12;
            amount = totalInvestment(monthly_investment, interest, period, on_interest);
        }
        if (period == 0) {
            amount = target;
            printf("%.2f \n" , amount);
        } else {
            printf("%.2f \n" , amount);
        }
    }
    return 0;
}
