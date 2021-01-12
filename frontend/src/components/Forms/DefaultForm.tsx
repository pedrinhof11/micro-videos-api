import { Grid, GridProps, makeStyles, Theme } from "@material-ui/core";
import React from "react";

const useStyles = makeStyles((theme: Theme) => ({
  gridItem: {
    padding: theme.spacing(1, 0),
  },
}));

interface DefaultFormProps
  extends React.DetailedHTMLProps<
    React.FormHTMLAttributes<HTMLFormElement>,
    HTMLFormElement
  > {
  GridContainerProps?: GridProps;
  GridItemProps?: GridProps;
}
const DefaultForm: React.FC<DefaultFormProps> = ({
  GridContainerProps,
  GridItemProps,
  ...props
}) => {
  const classes = useStyles();
  return (
    <form {...props}>
      <Grid container {...GridContainerProps}>
        <Grid item {...GridItemProps} className={classes.gridItem}>
          {props.children}
        </Grid>
      </Grid>
    </form>
  );
};

export default DefaultForm;
