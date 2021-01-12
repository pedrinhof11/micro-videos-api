import { Box, Button, ButtonProps, makeStyles, Theme } from "@material-ui/core";
import React from "react";

const useStyles = makeStyles((theme: Theme) => ({
  submit: {
    margin: theme.spacing(1),
  },
}));

interface SubmitActionsProps {
  disabled?: boolean;
  handleSave: () => void;
}
const SubmitActions: React.FC<SubmitActionsProps> = (props) => {
  const classes = useStyles();

  const buttonProps: ButtonProps = {
    className: classes.submit,
    color: "secondary",
    variant: "contained",
    disabled: props.disabled,
  };

  return (
    <Box dir="rtl">
      <Button {...buttonProps} onClick={props.handleSave}>
        Salvar
      </Button>
      <Button {...buttonProps} type="submit">
        Salvar e continuar editado
      </Button>
    </Box>
  );
};

export default SubmitActions;
