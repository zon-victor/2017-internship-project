#include "includes/affordability.h"

int         main(int ac, char **av) 
{
    int     affordability;
    char    *category;
    char    *sector;
    double  net_salary;
    double  amount;
    
    if (ac != 5)
    {
        printf("Usage: ./affordability sector category net_salary amount\n");
        printf("Usage example: ./affordability 'government' 'insurance' '26000' '2400'\n");
        return (0);
    }
    sector = av[1];
    category = av[2];
    net_salary = atof(av[3]);
    amount = atof(av[4]);
    affordability = checkAffordability(sector, category, net_salary, amount);//1 = UNSUCCESSFUL... 0 = SUCCESSFUL
    printf("%d\n", affordability);
    return (0);
}