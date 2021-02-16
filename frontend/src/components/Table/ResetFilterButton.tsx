import { IconButton, makeStyles, Theme, Tooltip } from "@material-ui/core";
import ClearAllIcon from "@material-ui/icons/ClearAll";
import React from "react";

const useStyles = makeStyles((theme: Theme) => ({
  iconButton: (theme.overrides as any).MUIDataTableToolbar.icon,
}));

interface ResetFilterButtonProps {
  onClick: () => void;
}

const ResetFilterButton: React.FC<ResetFilterButtonProps> = (props) => {
  const classes = useStyles();
  return (
    <Tooltip title="Limpar busca">
      <IconButton className={classes.iconButton} onClick={props.onClick}>
        <ClearAllIcon />
      </IconButton>
    </Tooltip>
  );
};

export default ResetFilterButton;
