import { yupResolver } from "@hookform/resolvers/yup";
import {
  FormControl,
  FormControlLabel,
  FormHelperText,
  FormLabel,
  Radio,
  RadioGroup,
  TextField,
} from "@material-ui/core";
import { useSnackbar } from "notistack";
import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useHistory, useParams } from "react-router-dom";
import BaseForm from "../../components/Forms/BaseForm";
import SubmitActions from "../../components/Forms/SubmitActions";
import CastMemberResource from "../../http/CastMemberResource";
import { CastMember } from "../../types/models";
import useIsMountedRef from "../../hooks/useIsMountedRef";
import { yup } from "../../utils/yup";

const castMembersValidation = yup.object().shape({
  name: yup.string().label("nome").required().max(255),
  type: yup.number().label("tipo").required(),
});

const CastMembersForm = () => {
  const {
    register,
    handleSubmit,
    getValues,
    setValue,
    errors,
    reset,
    watch,
    trigger,
  } = useForm({
    resolver: yupResolver(castMembersValidation),
  });

  const isMountedRef = useIsMountedRef();
  const history = useHistory();
  const snackbar = useSnackbar();
  const { id } = useParams<{ id: string }>();
  const [loading, setLoading] = useState<boolean>(false);
  const [castMember, setCastMember] = useState<CastMember | null>(null);

  useEffect(() => {
    register({ name: "type" });
  }, [register]);

  useEffect(() => {
    if (!id) {
      return;
    }

    (async () => {
      setLoading(true);
      try {
        const {
          data: { data },
        } = await CastMemberResource.get(id);
        if (isMountedRef) {
          setCastMember(data);
          reset(data as any);
        }
      } catch (error) {
        snackbar.enqueueSnackbar("Não foi possivel carregar as informações", {
          variant: "error",
        });
      } finally {
        setLoading(false);
      }
    })();
  }, [id, reset, snackbar, isMountedRef]);

  const onSubmit = async (formData: any, event?: React.BaseSyntheticEvent) => {
    setLoading(true);
    try {
      const {
        data: { data },
      } = !castMember
        ? await CastMemberResource.create(formData)
        : await CastMemberResource.update(castMember.id, formData);

      snackbar.enqueueSnackbar("Membro de elenco salvo com sucesso", {
        variant: "success",
      });
      if (event) {
        id
          ? history.replace(`/cast-members/${data.id}/edit`)
          : history.push(`/cast-members/${data.id}/edit`);
      } else {
        history.push("/cast-members");
      }
    } catch (error) {
      snackbar.enqueueSnackbar("Error ao tentar salvar membro de elenco", {
        variant: "error",
      });
    } finally {
      setLoading(false);
    }
  };

  const onSave = async () => {
    const isValid = await trigger();
    if (isValid) {
      onSubmit(getValues());
    }
  };

  const handleChangeType = (_: any, value: any) => {
    setValue("type", value);
  };

  return (
    <BaseForm
      GridItemProps={{ xs: 12, md: 6 }}
      onSubmit={handleSubmit(onSubmit)}
    >
      <TextField
        inputRef={register}
        error={errors.name !== undefined}
        helperText={errors.name?.message}
        disabled={loading}
        name="name"
        label="Nome"
        variant="outlined"
        fullWidth
        InputLabelProps={{ shrink: true }}
      />
      <FormControl
        margin="normal"
        error={errors.type !== undefined}
        disabled={loading}
      >
        <FormLabel component="legend">Tipo</FormLabel>
        <RadioGroup
          name="type"
          value={watch("type") + ""}
          onChange={handleChangeType}
        >
          <FormControlLabel value="1" control={<Radio />} label="Diretor" />
          <FormControlLabel value="2" control={<Radio />} label="Ator" />
        </RadioGroup>

        {errors.type !== undefined && (
          <FormHelperText id="type-helper-text">
            {errors.type.message}
          </FormHelperText>
        )}
      </FormControl>

      <SubmitActions disabled={loading} handleSave={onSave}></SubmitActions>
    </BaseForm>
  );
};

export default CastMembersForm;
