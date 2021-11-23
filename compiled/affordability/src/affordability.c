#include "includes/affordability.h"

int         checkAffordability(char *sector, char *category, double net_salary, double amount)
{
    double  net_take_home;
    double  limit;

    net_take_home = 2600.00; //R 2 600
    if (strcmp(sector, "government") == 0) {//FOR GOVERNMENT EMPLOYEES
        if ((net_salary > net_take_home) && ((net_salary - amount) >= net_take_home)) {
            return 0;
        } else {
            return 1;
        }
    } else {
        //FOR NON-GOVERNMENT EMPLOYEES
        if (strcmp(category, "insurance") == 0) {
            limit = (15.00 / 100.00) * net_salary;//INSURANCE MUST BE 15% or LESS OF THE NET SALARY
        } else {
            limit = (25.00 / 100.00) * net_salary;//OTHER DEDUCTIONS MAYBE BE 25% or LESS OF THE NET SALARY
        }
        if (amount <= limit) {
            return 0;
        } else {
            return 1;
        }
    }
}
