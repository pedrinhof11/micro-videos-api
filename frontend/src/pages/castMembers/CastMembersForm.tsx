import React, { useEffect } from "react";
import {
  TextField,
  Box,
  Button,
  ButtonProps,
  makeStyles,
  Theme,
  FormControl,
  FormControlLabel,
  FormLabel,
  Radio,
  RadioGroup,
} from "@material-ui/core";
import { useForm } from "react-hook-form";
import CastMemberResource from "../../http/CastMemberResource";

const useStyles = makeStyles((theme: Theme) => ({
  submit: {
    margin: theme.spacing(1),
  },
}));

const CastMembersForm = () => {
  const classes = useStyles();

  const buttonProps: ButtonProps = {
    variant: "outlined",
    className: classes.submit,
  };

  const { register, handleSubmit, getValues, setValue } = useForm();

  useEffect(() => {
    register({ name: "type" });
  }, [register]);

  const onSubmit = async (formData: any, event?: React.BaseSyntheticEvent) => {
    try {
      const { data } = await CastMemberResource.create(formData);
      console.log(data);
    } finally {
    }
  };

  const onSave = async () => onSubmit(getValues());

  const handleChangeType = (event: any) => {
    setValue("type", parseInt(event.target.value));
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <TextField
        inputRef={register}
        name="name"
        label="Nome"
        variant="outlined"
        fullWidth
      />

      <FormControl margin="normal">
        <FormLabel component="legend">Tipo</FormLabel>
        <RadioGroup name="type" onChange={handleChangeType}>
          <FormControlLabel value="1" control={<Radio />} label="Diretor" />
          <FormControlLabel value="2" control={<Radio />} label="Ator" />
        </RadioGroup>
      </FormControl>

      <Box dir="rtl">
        <Button {...buttonProps} onClick={onSave}>
          Salvar
        </Button>
        <Button {...buttonProps} type="submit">
          Salvar e continuar editado
        </Button>
      </Box>
    </form>
  );
};

export default CastMembersForm;
