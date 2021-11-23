
NAME = savings

SRC = src/main.c src/savings.c

INC = includes/savings.h

OBJ = $(SRC:.c=.o)

CFLAGS	= -Wall -Wextra -Werror

$(NAME): $(OBJ)
	@gcc $(OBJ) -o $(NAME) -I$(INC) $(CFLAGS)

all: $(NAME)

clean:
	@rm -rf $(OBJ)

fclean: clean
	@rm -rf $(NAME)

re: fclean $(NAME)
	
test:   re
	./$(NAME) 2400 y4 m0 0 goal yearly